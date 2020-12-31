<?php

class FaqController extends MyFaqAppController {

    public function index(){
        $this->set('title_for_layout', $this->Lang->get('FAQ'));
        $faqs = $this->Faq->find('all', ['order' => 'id DESC']);
        $this->set(compact("faqs"));
    }



    public function ajax_get_faq($id){

        $this->layout = null;

        $this->autoRender = false;



        $id = (((int)$id) == 0) ? 1 : (int)$id;



        $faq = $this->Faq->find('first', [

            'conditions' => [

                'id' => $id

            ],

        ]);



        if(!empty($faq))

            $faq = json_encode(current($faq), JSON_PRETTY_PRINT);

        else

            $faq = 0;



        $this->response->type('json');

        $this->response->body($faq);

    }



    public function admin_index(){

        if($this->isConnected AND $this->User->isAdmin()){

            $this->set('title_for_layout', $this->Lang->get('FAQ'));

            $this->layout = 'admin';

            $faqs = $this->Faq->find('all', [

                'order' => 'id'

            ]);

            $this->set(compact("faqs"));

        }

        else

            throw new ForbiddenException();



    }



    public function admin_ajax_save_faq(){

        if($this->isConnected AND $this->User->isAdmin()){

            if($this->request->is('post')){

                $this->layout = null;

                $this->autoRender = false;

                $data = $this->request->data;

                $return = 0;

                if($data['action'] == "edit") {

                    $data['id'] = (((int)$data['id']) == 0) ? 1 : (int)$data['id'];

                    $faq = $this->Faq->find('first', ['conditions' => ['id' => $data['id']]]);

                    $faq = current($faq);

                    $faq['question'] = $data['question'];

                    $faq['answer'] = $data['answer'];

                    if($this->Faq->save($faq)){

                        $return = json_encode($faq);

                    }

                    else

                        $return = 1;

                }else if($data['action'] == "add"){

                    $this->Faq->read(null, null);

                    $this->Faq->set([

                        "question" => $data['question'],

                        "answer" => $data['answer']

                    ]);

                    if($faq = $this->Faq->save()){

                        $return = json_encode([

                            "action" => "add",

                            "id" => $this->Faq->id,

                            "question" => $data['question'],

                            "answer" => $data['answer'],

                        ]);

                    }

                    else

                        $return = 1;

                }

            }

            else

                throw new ForbiddenException();



            $this->response->type('json');

            $this->response->body($return);

        }

    }



    public function admin_ajax_remove_faq(){

        if($this->isConnected AND $this->User->isAdmin()) {

            $this->layout = null;

            $this->autoRender = false;

            $return = 1;

            if ($this->request->is('post')) {

                if(isset($this->request->data['id'])){

                    $data = $this->request->data;

                    $id = (((int)$data['id']) == 0) ? "" : (int)$data['id'];

                    if(!empty($id) && is_int($id)){

                        $this->Faq->delete($id);

                        $return = 0;

                    }

                }

            }



            $this->response->type('json');

            $this->response->body($return);

        } else {

          throw new ForbiddenException();

        }

    }



}

