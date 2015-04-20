<?php

// Admin Navigation: add new item
Navigation::add(__('Question', 'question'), 'content', 'question', 10);

// Add actions
Action::add('admin_themes_extra_index_template_actions','QuestionAdmin::formComponent');
Action::add('admin_themes_extra_actions','QuestionAdmin::formComponentSave');
Action::add('admin_pre_render', 'QuestionAdmin::ajax');

class QuestionAdmin extends Backend
{
    public static function main()
    {
        $errors = array();
        
        $question = new Table('question');
        
        Breadcrumbs::add('index.php?id=question', __('Question', 'question'));

        // Check for get actions
        // -------------------------------------
        if (Request::get('action')) {
            switch (Request::get('action')) {
                
                // Edit
                // -------------------------------------
                case "edit":
                
                    if(Request::get('row_id')) {
                        
                        $id = (int)Request::get('row_id');
                        
                        if (Request::post('submit_save') or Request::post('submit_save_and_exit')) {
                            if (Security::check(Request::post('csrf'))) {
                            
                                $name = trim(Request::post('name'));
                                $date = trim(Request::post('date'));
                                $answer = trim(Request::post('answer'));
                                $message = trim(Request::post('message'));
                                
                                if (empty($name)) $errors['name_empty'] = __('Required field', 'question');
                                if (empty($date)) $errors['date_empty'] = __('Required field', 'question');
                                if (empty($message)) $errors['message_empty'] = __('Required field', 'question');
                                
                                if (Valid::date($date)) $date = strtotime($date);
                                else $date = time();
                                
                                if (count($errors) == 0) {
                                    
                                    $question->update($id, array(
                                        'name' => $name,
                                        'date' => $date,
                                        'message' => $message,
                                        'answer' => $answer,
                                        'check' => 1,
                                    ));
                                    
                                    Notification::set('success', __('Changes successfully saved', 'question'));
                            
                                    if (Request::post('submit_save'))
                                        Request::redirect('index.php?id=question&row_id=' . $id . '&action=edit');
                                    else
                                        Request::redirect('index.php?id=question');
                                }
                            } else { die('csrf detected!'); }
                        }
                        
                        $row = $question->select('[id='.$id.']', null);
                        
                        Breadcrumbs::add("index.php?id=question&row_id={$row['id']}&action=edit", $row['name']);
                        
                        if (Request::post('name')) $row['name'] = Request::post('name');
                        if (Request::post('date')) $row['date'] = Request::post('date');
                        if (Request::post('answer')) $row['answer'] = Request::post('answer');
                        if (Request::post('message')) $row['message'] = Request::post('message');
                    
                        View::factory('question/views/backend/edit')
                            ->assign('row', $row)
                            ->assign('errors', $errors)
                            ->display();
                    }
                    break;

                // Delete
                // -------------------------------------
                case "delete":
                    
                    if(Request::get('row_id')) {
                        if (Security::check(Request::get('token'))) {
                            
                            $id = (int)Request::get('row_id');
                            $question->delete($id);
                            
                            Notification::set('success', __('Removal was successful', 'question'));
                            Request::redirect('index.php?id=question' . ((Request::get('page')) ? '&page='.Request::get('page') : ''));
                            
                        } else { die('csrf detected!'); }
                    }
                    break;
                
                // Settings
                // -------------------------------------
                case "settings":
                    
                    Breadcrumbs::add('index.php?id=question&action=settings', __('Settings', 'question'));
                    
                    if (Request::post('submit_save') or Request::post('submit_save_and_exit')) {
                        if (Security::check(Request::post('csrf'))) {
                            
                            $form = (Request::post('form')) ? 'hide' : 'show';
                            $check = (Request::post('check')) ? 'yes' : 'no';
                            $double = (Request::post('double')) ? 'yes' : 'no';
                             
                            Option::update('question_template', Request::post('template'));
                            Option::update('question_limit', (int)Request::post('limit'));
                            Option::update('question_time', (int)Request::post('time'));
                            Option::update('question_email', Request::post('email'));
                            Option::update('question_form', $form);
                            Option::update('question_check', $check);
                            Option::update('question_double', $double);
                            
                            Notification::set('success', __('Changes successfully saved', 'question'));
                            
                            if (Request::post('submit_save'))
                                Request::redirect('index.php?id=question&action=settings');
                            else
                                Request::redirect('index.php?id=question');
                                
                        } else { die('csrf detected!'); }
                    }
                    
                    View::factory('question/views/backend/settings')->display();
                    break;
            }  
        } else {
        
            if (Request::post('submit_delete_question')) {
                if (Security::check(Request::post('csrf'))) {
                    $records_delete = Request::post('question_delete');
                    if (count($records_delete) > 0) {
                        foreach ($records_delete as $id) {
                            $question->delete($id);
                        }
                        Notification::set('success', __('Removal was successful', 'question'));
                        Request::redirect('index.php?id=question' . ((Request::get('page')) ? '&page='.Request::get('page') : ''));
                    }
                } else { die('csrf detected!'); }
            }
            
            $records_all = $question->select('', 'all', null, null, 'date', 'DESC');
            
            $page = (Request::get('page')) ? Request::get('page') : 1;
            
            $count = count($records_all);
            $limit = 10;
            $pages = ceil($count/$limit);
            $start = ($page-1)*$limit;
                
            if ($count > 0) $records = array_slice($records_all, $start, $limit);  
            else $records = array();
            
            View::factory('question/views/backend/index')
                ->assign('records', $records)
                ->assign('current_page', $page)
                ->assign('pages_count', $pages)
                ->display();
        }
    }
    
    public static function ajax() {
    
        if (Request::post('question_important_id')) {
            $question = new Table('question');
            $question->update((int)Request::post('question_important_id'), array('important' => (int)Request::post('val'), 'check' => 1));
            exit(__('Saved', 'question'));
        }
        
        if (Request::post('question_check_id')) {
            $question = new Table('question');
            $question->update((int)Request::post('question_check_id'), array('check' => 1));
            exit(__('Saved', 'question'));
        }
    }
    
    /**
     * Get Templates
     */
    public static function getTemplates()
    {
        $_templates = Themes::getTemplates();
        foreach ($_templates as $template) {
            $templates[basename($template, '.template.php')] = basename($template, '.template.php');
        }
        return $templates;
    }
    
    /**
     * Form Component Save
     */
    public static function formComponentSave()
    {
        if (Request::post('question_component_save')) {
            if (Security::check(Request::post('csrf'))) {
                Option::update('question_template', Request::post('question_form_template'));
                Request::redirect('index.php?id=themes');
            }
        }
    }

    /**
     * Form Component
     */
    public static function formComponent()
    {
        echo (
            Form::open().
            Form::hidden('csrf', Security::token()).
            Form::label('question_form_template', __('Question', 'question')).
            Form::select('question_form_template', QuestionAdmin::getTemplates(), Option::get('question_template')).
            Html::br().
            Form::submit('question_component_save', __('Save', 'question'), array('class' => 'btn')).
            Form::close()
        );
    }

}