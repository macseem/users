<?php
/**
 * Created by PhpStorm.
 * User: macseem
 * Date: 1/25/15
 * Time: 12:55 PM
 */

namespace app\controllers;


use app\models\Auth;
use app\models\Interest;
use app\models\Profile;
use app\models\User;
use app\models\UserInterest;
use jf\Controller;
use jf\Core;
use jf\Exception;

class UserController extends Controller{

    public $layout = "main";

    public function actionSignUp()
    {
        $message = '';
        $defaultData = array(
            'login' => '',
            'password' => '',
            'email' => '',
            'name' => '',
            'city' => '',
            'about' => '',
        );
        $data = array_merge($defaultData, Core::$app->request->post);
        if(!empty(Core::$app->request->post)){
            $auth = new Auth();
            $profile = new Profile();
            try{
                $auth->fillFromRequest();
                $auth->cryptPassword();
                $auth->save();
                $profile->user_id = $auth->id;
                $profile->fillFromRequest();
                $profile->save();
                $userInterest = new UserInterest();
                $userInterest->user_id = $auth->id;
                $interestModel = new Interest();
                if(!empty(Core::$app->request->post['interest'][0])){
                    foreach(Core::$app->request->post['interest'] as $interest) {
                        try{
                            $interestId = $interestModel->findOneBy('name', $interest)->id;
                        } catch(Exception $e) {
                            if($e->getCode() == Interest::EXCEPTION_NOT_VALID_ROW) {
                                $interestModel->isNew = true;
                                $interestModel->id = NULL;
                                $interestModel->name = $interest;
                                $interestModel->save();
                                $interestId = $interestModel->id;
                            }
                        }
                        $userInterest->isNew = true;
                        $userInterest->interest_id = $interestId;
                        $userInterest->save();
                    }
                }
                $this->redirect(Core::$app->router->getDefaultRoute());
            } catch(Exception $e){
                $message = $e->getMessage();
            }
        }
        Core::$app->request->post['msg'] = $message;
        return $this->render('user/signup',$data);
    }

    public function actionLogin()
    {
        $data = array_merge(array('login' => '', 'password' => ''), Core::$app->request->post);
        if(empty(Core::$app->request->post['login']) and empty(Core::$app->request->post['password']))
            return $this->render('user/login', $data);
        if(empty(Core::$app->request->post['password']))
            return $this->render('user/login', array_merge($data, array('msg' => 'Password is required')));
        if(empty(Core::$app->request->post['login']))
            return $this->render('user/login', array_merge($data, array('msg' => 'Login is required')));
        $auth = new Auth();
        try{
            $auth->findOneBy('login',Core::$app->request->post['login']);
        } catch(Exception $e) {
            return $this->render('user/login', array_merge($data, array('msg' => 'There is no '.Core::$app->request->post['login'].' user')));
        }
        if(!$auth->checkPassword(Core::$app->request->post['password']))
            return $this->render('user/login', array_merge($data, array('msg' => 'Login or password is invalid')));
        Core::$app->user->signIn($auth);
        return $this->redirect(Core::$app->router->getDefaultRoute());
    }

    public function actionLogout()
    {
        Core::$app->user->logout();
        return $this->redirect('/user/login');
    }

    public function actionSearch()
    {
        //TODO: Implement Alone In City Checkbox
        //TODO: Implement minimum interests count field
        return $this->render('user/search');
    }

}