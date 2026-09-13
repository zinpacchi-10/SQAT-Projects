<?php
require_once APP_ROOT . '/app/core/Controller.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();

        $featured = $productModel->featured();
        if (empty($featured)) {
            $featured = array_slice($productModel->browse(['sort' => 'newest']), 0, 8);
        }
        $categories = $categoryModel->topLevel();

        $this->view('home/index', compact('featured', 'categories'));
    }
}
