<?php


namespace Okay\Modules\SimplaMarket\Redirects\Extensions;


use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\EntityFactory;
use Okay\Modules\SimplaMarket\Redirects\Entities\RedirectsEntity;
use Okay\Core\Config;
use Okay\Core\Request;

class Redirects implements ExtensionInterface
{

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        EntityFactory $entityFactory,
        Request       $request
    ) {
        $this->entityFactory = $entityFactory;
        $this->request       = $request;
    }

    public function redirect()
    {
        // Here we store the URLs that shouldn't be processed:
        $dont_change_this_urls = [
            'payment/SimplaMarket/PrivatPayPart/create_payment'
        ];

        $user_url = $this->request->getRequestUri();

        if (in_array($user_url, $dont_change_this_urls)) {
            $url = $user_url;
        } else {
            $url = $this->nice_url($user_url);
        }

        /** @var RedirectsEntity $redirectsEntity */
        $redirectsEntity = $this->entityFactory->get(RedirectsEntity::class);

        $redirect = $redirectsEntity->findOne([
            'url_from' => $url,
            'enabled' => 1,
        ]);

        if (!empty($redirect)) {
            header( 'Location: '.$this->getProtocol().$this->getDomain().'/'.$redirect->url_to, true, $redirect->status);
            exit();
        } elseif(($user_url != $url) || ($this->getDomain() != rtrim($_SERVER['HTTP_HOST']))) {
            header( 'Location: '.$this->getProtocol().$this->getDomain().'/'.$url, true, 301);
            exit();
        }
    }

    private static function getDomain()
    {
        return preg_replace('/^www\./', '', rtrim($_SERVER['HTTP_HOST']));
    }

    private static function getProtocol() //todo сделать такой метод из Request публичным и использовать его. Не забыть про :// после протокола
    {
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5)) == 'https' ? 'https' : 'http';
        if($_SERVER["SERVER_PORT"] == 443)
            $protocol = 'https';
        elseif (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1')))
            $protocol = 'https';
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
            $protocol = 'https';
        return (!empty($protocol)?$protocol.'://':'');
    }

    private function nice_url($url)
    {
        $parsedUrl = parse_url($url);
        $nice_url = strtolower(strval($parsedUrl['path']));  //меняем только основную часть url
        if (isset($parsedUrl['query']) && $parsedUrl['query']) {
            $nice_url .= '?' . $parsedUrl['query'];  //get-запрос, если он был, оставляем оригинальным
        }
        return $nice_url;
    }
}