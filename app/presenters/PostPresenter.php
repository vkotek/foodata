<?php
namespace App\Presenters;

use Nette,
    Nette\Application\UI\Form;

class PostPresenter extends BasePresenter
{
    //*@var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderShow($postId)
    {
        $post = $this->database->table('posts')->get($postId);
        
        if (!$post) {
            $this->error('Tak tady nic není, zkus to jinde..');
        }
        
        $this->template->post = $post;
        $this->template->comments = $post->related('comment')->order('created_at');
        
    }
    
    protected function createComponentCommentForm()
    {
    
        $form = new Form;
        
        $form->addText('name','Jméno')
            ->setRequired();
        
        $form->addText('email','Email');
        
        $form->addTextArea('comment','Komentář')
            ->setRequired();
        
        $form->addSubmit('send','Odeslat');
        
        $form->onSuccess[] = [$this, 'commentFormSucceeded'];
        
        return $form;
        
    }
    
    public function commentFormSucceeded($form, $values)
    {
        $postId = $this->getParameter('postId');
        
        $this->database->table('comments')->insert([
            'post_id' => $postId,
            'name' => $values->name,
            'email' => $values->email,
            'content' => $values->comment,
        ]);
        
        $this->flashMessage('thx for comment bro.','success');
        $this->redirect('this');
    }
    
    protected function createComponentPostForm()
    {
        $form = new Form;
        
        $form->addText('title','Nazev')
            ->setRequired();
        
        $form->addTextArea('content','O produktu');
        
        $form->addText('barcode','Carkovy kod')
            ->addRule(Form::INTEGER, 'Carkovy kod musi byt numericky');
        
        $form->addSubmit('send','Ulozit a publikovat');
        $form->onSuccess[] = [$this, 'postFormSucceeded'];
        
        return $form;
    }
    
    public function postFormSucceeded($form, $values)
    {
        $postId = $this->getParameter('postId');
        
        if ($postId) {
            $post = $this->database->table('posts')->get($postId);
            $post->update($values);
        } else {
            $post = $this->database->table('posts')->insert($values);
        }
        
        $this->flashMessage('Product added','success');
        $this->redirect('show', $post->id);
    }
    
    public function actionEdit($postId)
    {
        $post = $this->database->table('posts')->get($postId);
        if (!$post) {
            $this->error('post not found');
        }
        $this['postForm']->setDefaults($post->toArray());
    }
    
    
}
