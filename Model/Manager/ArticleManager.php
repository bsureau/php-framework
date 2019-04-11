<?php

namespace Model\Manager;

require_once __DIR__ . '/../../Model/Exceptions/ArticleNotFoundException.php';
require_once __DIR__ . '/../../Model/Manager/DbManager.php';
require_once __DIR__ . '/../../Model/Interfaces/ItemInterface.php';
require_once __DIR__ . '/../../Model/Article/Article.php';

use Model\Article\Article;
use Model\Interfaces\ItemInterface;
use Model\Exceptions\ArticleNotFoundException;

/**
 * Manage article from database
 * 
 * Class ArticleManager
 * @package Manager
 */
abstract class ArticleManager extends DbManager implements ItemInterface
{
    /**
     * @param int $id
     * @return mixed|Article
     * @throws ArticleNotFoundException
     * @throws \Exception
     */
    public static function getById($id)
    {
        // Select article into database
        $stmt = self::getDb()->prepare("
          SELECT id, title, image, head, content, create_date
          FROM articles
          WHERE id = :id;
        ");
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        // Throw an exception if the article hasn't been found
        if ($stmt->rowCount() === 0) {
            throw new ArticleNotFoundException($id);
        }

        // Get new article
        $article = new Article();
        $article->hydrate(
            $stmt->fetch(\PDO::FETCH_ASSOC)
        );

        return $article;
    }

    /**
     * @param null $offset
     * @param null $limit
     * @return array|mixed
     * @throws \Exception
     */
    public static function getAll($offset = null, $limit = null)
    {
        // Select list of article in database
        $stmt = self::getDb()->prepare("
          SELECT id, title, image, head, content, create_date
          FROM articles;
        ");
        $stmt->execute();

        // Instantiates a collection of article
        $articles = array();
        while ($articleData = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $article = new Article();
            $article->hydrate($articleData);
            $articles[] = $article;
        }

        return $articles;
    }

    /**
     * @param $expr
     * @return array OR null if 0 result found by query
     * @throws \Exception
     */
    public static function searchArticles($expr){
        $stmt = self::getDb()->prepare("SELECT DISTINCT articles.id, title, image, head, content, create_date 
                                                  FROM articles 
                                                  JOIN users ON id_creator = users.id
                                                  WHERE name LIKE :expr OR title LIKE :expr OR head LIKE :expr");
        $stmt->bindValue(':expr', '%'.$expr.'%');
        $stmt->execute();
        $articles = null;
        while ($articleData = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $article = new Article();
            $article->hydrate($articleData);
            $articles[] = $article;
        }
        return $articles;
    }
}