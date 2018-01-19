<?php
/**
 * Created by PhpStorm.
 * User: Aleksandr Sazanovich
 * Date: 11.01.18
 * Time: 0:32
 */


namespace CTOBundle\Services;

use Lsw\ApiCallerBundle\Call\HttpGetJson;
use Lsw\ApiCallerBundle\Caller\ApiCallerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NewsAPIService{

 private $newsApiUrl;
 private $newsApiKey;
 private $container;
 private $data;
    /**
     * NewsAPIService constructor.
     * @param $newsApiUrl
     * @param $newsApiKey
     */
    public function __construct(ContainerInterface $container, $page = 1)
    {
        $this->container = $container->get("api_caller");
        $this->newsApiKey = "ab928a4777004152904498ef316830e1";
        $this->newsApiUrl = "https://newsapi.org/v2/everything";
        $this->data = $this->getObjects($page);
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data->articles;
    }

    /**
     * @return mixed
     */
    public function getNewsApiUrl()
    {
        return $this->newsApiUrl;
    }

    /**
     * @return mixedc
     */
    public function getNewsApiKey()
    {
        return $this->newsApiKey;
    }

    public function getObjects($page = 1) {
        $parameters = array(
            "apiKey" => $this->getNewsApiKey(),
            "language" => "ru",
            "sortBy" => "popularity",
            "sources" => "bbc",
            "page"    => $page
        );
        $jsonOut = $this->container->call(new HttpGetJson($this->getNewsApiUrl(), $parameters));
        return $jsonOut;
    }

}