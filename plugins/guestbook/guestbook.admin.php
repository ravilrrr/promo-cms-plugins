<?php

// Admin Navigation: add new item
Navigation::add(__('Guestbook', 'guestbook'), 'content', 'guestbook', 10);

// Add actions
Action::add('admin_themes_extra_index_template_actions','GuestbookAdmin::formComponent');
Action::add('admin_themes_extra_actions','GuestbookAdmin::formComponentSave');
Action::add('admin_pre_render', 'GuestbookAdmin::ajax');

class GuestbookAdmin extends Backend
{
    public static function main()
    {
        $errors = array();
        
        $guestbook = new Table('guestbook');
        
        Breadcrumbs::add('index.php?id=guestbook', __('Guestbook', 'guestbook'));

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
                                
                                if (empty($name)) $errors['name_empty'] = __('Required field', 'guestbook');
                                if (empty($date)) $errors['date_empty'] = __('Required field', 'guestbook');
                                if (empty($message)) $errors['message_empty'] = __('Required field', 'guestbook');
                                
                                if (Valid::date($date)) $date = strtotime($date);
                                else $date = time();
                                
                                if (count($errors) == 0) {
                                    
                                    $guestbook->update($id, array(
                                        'name' => $name,
                                        'date' => $date,
                                        'message' => $message,
                                        'answer' => $answer,
                                        'check' => 1,
                                    ));
                                    
                                    Notification::set('success', __('Changes successfully saved', 'guestbook'));
                            
                                    if (Request::post('submit_save'))
                                        Request::redirect('index.php?id=guestbook&row_id=' . $id . '&action=edit');
                                    else
                                        Request::redirect('index.php?id=guestbook');
                                }
                            } else { die('csrf detected!'); }
                        }
                        
                        $row = $guestbook->select('[id='.$id.']', null);
                        
                        Breadcrumbs::add("index.php?id=guestbook&row_id={$row['id']}&action=edit", $row['name']);
                        
                        if (Request::post('name')) $row['name'] = Request::post('name');
                        if (Request::post('date')) $row['date'] = Request::post('date');
                        if (Request::post('answer')) $row['answer'] = Request::post('answer');
                        if (Request::post('message')) $row['message'] = Request::post('message');
                    
                        View::factory('guestbook/views/backend/edit')
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
                            $guestbook->delete($id);
                            
                            Notification::set('success', __('Removal was successful', 'guestbook'));
                            Request::redirect('index.php?id=guestbook' . ((Request::get('page')) ? '&page='.Request::get('page') : ''));
                            
                        } else { die('csrf detected!'); }
                    }
                    break;
                
                // Settings
                // -------------------------------------
                case "settings":
                    
                    Breadcrumbs::add('index.php?id=guestbook&action=settings', __('Settings', 'guestbook'));
                    
                    if (Request::post('submit_save') or Request::post('submit_save_and_exit')) {
                        if (Security::check(Request::post('csrf'))) {
                            
                            $form = (Request::post('form')) ? 'hide' : 'show';
                            $check = (Request::post('check')) ? 'yes' : 'no';
                            $double = (Request::post('double')) ? 'yes' : 'no';
                             
                            Option::update('guestbook_template', Request::post('template'));
                            Option::update('guestbook_limit', (int)Request::post('limit'));
                            Option::update('guestbook_time', (int)Request::post('time'));
                            Option::update('guestbook_email', Request::post('email'));
                            Option::update('guestbook_form', $form);
                            Option::update('guestbook_check', $check);
                            Option::update('guestbook_double', $double);
                            
                            Notification::set('success', __('Changes successfully saved', 'guestbook'));
                            
                            if (Request::post('submit_save'))
                                Request::redirect('index.php?id=guestbook&action=settings');
                            else
                                Request::redirect('index.php?id=guestbook');
                                
                        } else { die('csrf detected!'); }
                    }
                    
                    View::factory('guestbook/views/backend/settings')->display();
                    break;
            }  
        } else {
        
            if (Request::post('submit_delete_guestbook')) {
                if (Security::check(Request::post('csrf'))) {
                    $records_delete = Request::post('guestbook_delete');
                    if (count($records_delete) > 0) {
                        foreach ($records_delete as $id) {
                            $guestbook->delete($id);
                        }
                        Notification::set('success', __('Removal was successful', 'guestbook'));
                        Request::redirect('index.php?id=guestbook' . ((Request::get('page')) ? '&page='.Request::get('page') : ''));
                    }
                } else { die('csrf detected!'); }
            }
            
            $records_all = $guestbook->select('', 'all', null, null, 'date', 'DESC');
            
            $page = (Request::get('page')) ? Request::get('page') : 1;
            
            $count = count($records_all);
            $limit = 10;
            $pages = ceil($count/$limit);
            $start = ($page-1)*$limit;
                
            if ($count > 0) $records = array_slice($records_all, $start, $limit);  
            else $records = array();
            
            View::factory('guestbook/views/backend/index')
                ->assign('records', $records)
                ->assign('current_page', $page)
                ->assign('pages_count', $pages)
                ->display();
        }
    }
    
    public static function ajax() {
    
        if (Request::post('guestbook_important_id')) {
            $guestbook = new Table('guestbook');
            $guestbook->update((int)Request::post('guestbook_important_id'), array('important' => (int)Request::post('val'), 'check' => 1));
            exit(__('Saved', 'guestbook'));
        }
        
        if (Request::post('guestbook_check_id')) {
            $guestbook = new Table('guestbook');
            $guestbook->update((int)Request::post('guestbook_check_id'), array('check' => 1));
            exit(__('Saved', 'guestbook'));
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
        if (Request::post('guestbook_component_save')) {
            if (Security::check(Request::post('csrf'))) {
                Option::update('guestbook_template', Request::post('guestbook_form_template'));
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
            Form::label('guestbook_form_template', __('Guestbook', 'guestbook')).
            Form::select('guestbook_form_template', GuestbookAdmin::getTemplates(), Option::get('guestbook_template')).
            Html::br().
            Form::submit('guestbook_component_save', __('Save', 'guestbook'), array('class' => 'btn')).
            Form::close()
        );
    }

}