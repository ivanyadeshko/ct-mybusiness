<?php

namespace backend\controllers;

use backend\models\EatAppleForm;
use Yii;
use yii\base\InlineAction;
use yii\base\InvalidArgumentException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use yii\data\ActiveDataProvider;
use common\models\Apple;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'grow-apples', 'fall-to-ground-apple', 'eat-apple'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, InlineAction $action) {
                            /** @var User $user */
                            $user = Yii::$app->user->identity;
                            return $action->id === 'logout' || ($user && $user->isAdmin());
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'grow-apples' => ['post'],
                    'fall-to-ground-apple' => ['post'],
                    'eat-apple' => ['post'],
                ],
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        $activeDataProvider = new ActiveDataProvider([
            'query' => Apple::find(),
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC
                ]
            ],
        ]);


        return $this->render('index', [
            'activeDataProvider' => $activeDataProvider,
        ]);
    }


    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm([
            'scenario' => LoginForm::SCENARIO_LOGIN_AS_ADMIN,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Generate random apples
     *
     * @return \yii\web\Response
     */
    public function actionGrowApples()
    {
        $total = rand(
            Yii::$app->params['appleGenerator']['valueMin'],
            Yii::$app->params['appleGenerator']['valueMax']
        );
        $limit = $generated = 0;
        $errors = [];
        while ($limit++ < $total) {

            $model = new Apple(
                Apple::getColors()[array_rand(Apple::getColors())]
            );

            if (!$model->save()) {
                $errors[] = sprintf("#%d. %s", $limit, Html::errorSummary($model));
            } else {
                $generated += 1;
            }

        }

        Yii::$app->session->setFlash(
            ($generated === $total ? 'success' : 'warning'),
            sprintf('Generated %d of %d apples', $generated, $total)
        );

        if ($errors) {
            Yii::$app->session->setFlash('danger', sprintf("Errors: %s", implode(", ", $errors)));
        }

        return $this->redirect(Url::toRoute('site/index'));
    }


    /**
     * Try to fall to ground apple
     *
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionFallToGroundApple(int $id)
    {
        $apple = Apple::findOne($id);
        if (!$apple) {
            throw new InvalidArgumentException(sprintf("Wrong apple ID: '%d'", $id));
        }
        if (!$apple->isOnTree()) {
            Yii::$app->session->setFlash('danger', sprintf('Apple #%d already on the ground', $id));
        } else {
            $apple->fallToGround();
            if ($apple->save()) {
                Yii::$app->session->setFlash('success', sprintf('Apple #%d dropped.', $id));
            } else {
                Yii::$app->session->setFlash('danger', sprintf('Cant drop the apple #%d. Reason: %s', $id, Html::errorSummary($apple)));
            }

        }
        return $this->redirect(Url::toRoute('site/index'));
    }


    /**
     * @return \yii\web\Response
     */
    public function actionEatApple()
    {
        $model = new EatAppleForm();
        if ($model->load(Yii::$app->request->post()) && $model->eat()) {
            Yii::$app->session->setFlash('success',
                sprintf('Apple #%d eaten by %d%%. Remaining: %d%%',
                    $model->id,
                    $model->size,
                    $model->getApple()->size * 100
                )
            );
        } else {
            Yii::$app->session->setFlash('danger',
                sprintf('Cant eat an apple #%d. Reason: %s', $model->id, Html::errorSummary($model))
            );
        }
        return $this->redirect(Url::toRoute('site/index'));
    }


    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
