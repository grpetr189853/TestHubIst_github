<?php

namespace app\controllers;

use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\SignupAdminForm;
use app\models\SignupStudentForm;
use app\models\SignupTeacherForm;
use app\models\User;
use app\models\VerifyEmailForm;
use DateTime;
use InvalidArgumentException;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'about'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['about'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return User::isUserAdmin(Yii::$app->user->identity->username);
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
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
//            'error' => [
//                'class' => 'yii\web\ErrorAction',
//            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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
        if(!Yii::$app->user->isGuest){
            if (User::isUserStudent(Yii::$app->user->identity->username)) {
                $this->redirect(array(
                    'student-test/index',
                    'status' => 'notpassed'
                ));
            }
            if (User::isUserAdmin(Yii::$app->user->identity->username) || User::isUserTeacher(Yii::$app->user->identity->username)) {
                $this->redirect(array(
                    'test/index'
                ));
            }
        }

        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionGetServerTime()
    {
        $now = new DateTime();
        echo $now->format("M j, Y H:i:s O");
    }

    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('passwordResetRequestForm', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');
            return $this->goHome();
        }

        return $this->render('resetPasswordForm', [
            'model' => $model,
        ]);
    }

    /**
     * Signs amin up.
     *
     * @return mixed
     */
    public function actionSignupAdmin()
    {
        $model = new SignupAdminForm();
        $role = "Администратор";
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration as Admin. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
            'role'  => $role,
        ]);
    }

    /**
     * Signs teacher up.
     *
     * @return mixed
     */
    public function actionSignupTeacher()
    {
        $model = new SignupTeacherForm();
        $role = "Переподаватель";
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration as Teacher. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
            'role'  => $role,
        ]);
    }

    /**
     * Signs Student up.
     *
     * @return mixed
     */
    public function actionSignupStudent()
    {
        $model = new SignupStudentForm();
        $role = "Студент";
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration as Student. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
            'role'  => $role,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }


    public function actionError()
    {
        if ($error = Yii::$app->errorHandler->exception) {
            if (Yii::$app->request->isAjax) {
                echo $error['message'];
            } else {
                $defaultMessage = 'Сервис временно испытывает проблемы. Приносим извинения за временные неудобства.';

                $layout = 'error';

                switch ($error->statusCode) {
                    case '404':
                        $layout = 'error404';
                        break;
                    /*
                    case '500':
                        $error['message'] = $defaultMessage;
                        break;
                    case '400':
                        $error['message'] = 'Запрос введеный вами некорректен. Пожалуйста, убедитесь в его правильности.';
                        break;
                    case '403':
                        $message = $error->getMessage();
//                        $error['message'] = 'У вас нет прав доступа на эту страницу.';
                        break;
                    default:
                        $error['message'] = 'Возникла непредвиденная ошибка. Приносим извинения за временные неудобства.';
                        break;
                    */
                }

                return $this->render($layout, $error);
            }
        }
    }

}
