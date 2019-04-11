<?php
/**
 * Created by PhpStorm.
 * User: benjaminsureau
 * Date: 10/10/2018
 * Time: 10:16
 */

namespace Controller;

use Model\Manager\ArticleManager;

require_once __DIR__ . '/../Controller/LayoutController.php';
require_once __DIR__ . '/../Model/Manager/ArticleManager.php';

/**
 * Class SearchController
 * @package Controller
 */
class SearchController extends LayoutController
{
    /**
     * @return mixed|void
     * @throws \View\Exceptions\ViewException
     */
    public function viewAction()
    {
        if(isset($_POST['s']) and !empty($_POST['s'])){
            $search = $_POST['s'];
            $articles = ArticleManager::searchArticles($search);
            $params = ['articles' => $articles];
            $this->render('home', $params);
        }else{
            $params = [ 'articles' => ArticleManager::getAll() ];
            $this->render('home', $params);
        }
    }
}