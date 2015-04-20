<?php

// Admin Navigation: add new item
Navigation::add(__('Reviews', 'reviews'), 'content', 'reviews', 10);

// Add actions
Action::add('admin_themes_extra_index_template_actions','ReviewsAdmin::formComponent');
Action::add('admin_themes_extra_actions','ReviewsAdmin::formComponentSave');
Action::add('admin_pre_render', 'ReviewsAdmin::ajax');

class ReviewsAdmin extends Backend
{
    public static function main()
    {
        $errors = array();
        
        $reviews = new Table('reviews');
        
        Breadcrumbs::add('index.php?id=reviews', __('Reviews', 'reviews'));

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
                                
                                if (empty($name)) $errors['name_empty'] = __('Required field', 'reviews');
                                if (empty($date)) $errors['date_empty'] = __('Required field', 'reviews');
                                if (empty($message)) $errors['message_empty'] = __('Required field', 'reviews');
                                
                                if (Valid::date($date)) $date = strtotime($date);
                                else $date = time();
                                
                                if (count($errors) == 0) {
                                    
                                    $reviews->update($id, array(
                                        'name' => $name,
                                        'date' => $date,
                                        'message' => $message,
                                        'answer' => $answer,
                                        'check' => 1,
                                    ));
                                    
                                    Notification::set('success', __('Changes successfully saved', 'reviews'));
                            
                                    if (Request::post('submit_save'))
                                        Request::redirect('index.php?id=reviews&row_id=' . $id . '&action=edit');
                                    else
                                        Request::redirect('index.php?id=reviews');
                                }
                            } else { die('csrf detected!'); }
                        }
                        
                        $row = $reviews->select('[id='.$id.']', null);
                        
                        Breadcrumbs::add("index.php?id=reviews&row_id={$row['id']}&action=edit", $row['name']);
                        
                        if (Request::post('name')) $row['name'] = Request::post('name');
                        if (Request::post('date')) $row['date'] = Request::post('date');
                        if (Request::post('answer')) $row['answer'] = Request::post('answer');
                        if (Request::post('message')) $row['message'] = Request::post('message');
                    
                        View::factory('reviews/views/backend/edit')
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
                            $reviews->delete($id);
                            
                            Notification::set('success', __('Removal was successful', 'reviews'));
                            Request::redirect('index.php?id=reviews' . ((Request::get('page')) ? '&page='.Request::get('page') : ''));
                            
                        } else { die('csrf detected!'); }
                    }
                    break;
                
                // Settings
                // -------------------------------------
                case "settings":
                    
                    Breadcrumbs::add('index.php?id=reviews&action=settings', __('Settings', 'reviews'));
                    
                    if (Request::post('submit_save') or Request::post('submit_save_and_exit')) {
                        if (Security::check(Request::post('csrf'))) {
                            
                            $form = (Request::post('form')) ? 'hide' : 'show';
                            $check = (Request::post('check')) ? 'yes' : 'no';
                            $double = (Request::post('double')) ? 'yes' : 'no';
                             
                            Option::update('reviews_template', Request::post('template'));
                            Option::update('reviews_limit', (int)Request::post('limit'));
                            Option::update('reviews_time', (int)Request::post('time'));
                            Option::update('reviews_email', Request::post('email'));
                            Option::update('reviews_form', $form);
                            Option::update('reviews_check', $check);
                            Option::update('reviews_double', $double);
                            
                            Notification::set('success', __('Changes successfully saved', 'reviews'));
                            
                            if (Request::post('submit_save'))
                                Request::redirect('index.php?id=reviews&action=settings');
                            else
                                Request::redirect('index.php?id=reviews');
                                
                        } else { die('csrf detected!'); }
                    }
                    
                    View::factory('reviews/views/backend/settings')->display();
                    break;
            }  
        } else {
        
            if (Request::post('submit_delete_reviews')) {
                if (Security::check(Request::post('csrf'))) {
                    $records_delete = Request::post('reviews_delete');
                    if (count($records_delete) > 0) {
                        foreach ($records_delete as $id) {
                            $reviews->delete($id);
                        }
                        Notification::set('success', __('Removal was successful', 'reviews'));
                        Request::redirect('index.php?id=reviews' . ((Request::get('page')) ? '&page='.Request::get('page') : ''));
                    }
                } else { die('csrf detected!'); }
            }
            
            $records_all = $reviews->select('', 'all', null, null, 'date', 'DESC');
            
            $page = (Request::get('page')) ? Request::get('page') : 1;
            
            $count = count($records_all);
            $limit = 10;
            $pages = ceil($count/$limit);
            $start = ($page-1)*$limit;
                
            if ($count > 0) $records = array_slice($records_all, $start, $limit);  
            else $records = array();
            
            View::factory('reviews/views/backend/index')
                ->assign('records', $records)
                ->assign('current_page', $page)
                ->assign('pages_count', $pages)
                ->display();
        }
    }
    
    public static function ajax() {
    
        if (Request::post('reviews_important_id')) {
            $reviews = new Table('reviews');
            $reviews->update((int)Request::post('reviews_important_id'), array('important' => (int)Request::post('val'), 'check' => 1));
            exit(__('Saved', 'reviews'));
        }
        
        if (Request::post('reviews_check_id')) {
            $reviews = new Table('reviews');
            $reviews->update((int)Request::post('reviews_check_id'), array('check' => 1));
            exit(__('Saved', 'reviews'));
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
        if (Request::post('reviews_component_save')) {
            if (Security::check(Request::post('csrf'))) {
                Option::update('reviews_template', Request::post('reviews_form_template'));
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
            Form::label('reviews_form_template', __('Reviews', 'reviews')).
            Form::select('reviews_form_template', ReviewsAdmin::getTemplates(), Option::get('reviews_template')).
            Html::br().
            Form::submit('reviews_component_save', __('Save', 'reviews'), array('class' => 'btn')).
            Form::close()
        );
    }

}