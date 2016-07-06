<?php
namespace App\Presenters;

use Nette,
    Nette\Application\UI\Form;

class ProductPresenter extends BasePresenter
{
    //*@var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderShow($productId)
    {
        $this->template->product = $this->database->table('quickstart')->get($productId);
    }
}
