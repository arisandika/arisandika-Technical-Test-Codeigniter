<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');

        $this->session    = \Config\Services::session();
        $this->parser     = \Config\Services::parser();
        $this->validation = \Config\Services::validation();
        $this->email      = \Config\Services::email();
        $this->pager      = \Config\Services::pager();
        $this->language   = \Config\Services::language();

        $this->language->setLocale('id');
    }

    /**
     * Helper method untuk membuat HTML carousel produk.
     * Ditempatkan di BaseController agar bisa digunakan kembali oleh controller lain.
     */
    protected function _buildProductCarousel(string $sectionTitle, array $products): string
    {
        $productList = [];

        foreach ($products as $product) {
            $productList[] = [
                'product_url'   => $product['product_url'],
                'product_name'  => $product['product_name'],
                'product_image' => $product['product_image'],
                'product_price' => $product['product_price'],
            ];
        }

        $data = [
            'section_title' => $sectionTitle,
            'view_all_url'  => '#',
            'product_list'  => $productList,
            'base_url'      => base_url(),
        ];

        return $this->parser->setData($data)->render('front/components/product_carousel');
    }

    protected function _getProductsFromJson(): array
    {
        $filePath = ROOTPATH . 'public/data/products.json';

        if (! file_exists($filePath)) {
            return [];
        }

        $jsonString = file_get_contents($filePath);

        $products = json_decode($jsonString, true);

        return $products ?? [];
    }
}
