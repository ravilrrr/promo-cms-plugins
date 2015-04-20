<?php

// В меню админки добавляем новый элемент
Navigation::add(__('Forms', 'forms'), 'content', 'forms', 10);

// Добавляем экшн, чтобы перехватить AJAX перед выводом дизайна админки
Action::add('admin_pre_render','FormsAdmin::ajax');

class FormsAdmin extends Backend
{
    public static function main()
    {
        $errors = array();
        
        $forms = new Table('forms');
        
        Breadcrumbs::add('index.php?id=forms', __('Forms', 'forms'));

        // Check for get actions
        // -------------------------------------
        if (Request::get('action')) {
            switch (Request::get('action')) {
            
                // Демо данные
                // URL: http://siteurl/admin/index.php?id=forms&action=demo
                // -------------------------------------
                case "demo":
                    include PLUGINS . DS . 'forms' . DS . 'install' . DS . 'forms.demo.php';
                    Notification::set('success', __('Changes successfully saved', 'forms'));
                    Request::redirect('index.php?id=forms');
                    break;

                // Настройки формы
                // -------------------------------------
                case "form":
                    
                    // Если выбрана определенная форма
                    if(Request::get('form_id')) {
                        $form_id = (int)Request::get('form_id');
                        $form = $forms->select('[id='.$form_id.']', null);
                        
                        Breadcrumbs::add("index.php?id=forms&form_id={$form_id}&action=elements", __('Elements of the form', 'forms'));
                        Breadcrumbs::add("index.php?id=forms&form_id={$form_id}&action=form", __('Settings form', 'forms'));
                    
                    // Если форма не выбрана (например, при добавлении новой формы)
                    } else {
                        Breadcrumbs::add("index.php?id=forms&action=form", __('Add form', 'forms'));
                        
                        $form['name'] = '';
                        $form['email'] = Option::get('system_email');
                        $form['button'] = __('Button text default', 'forms');
                        $form['subject'] = __('Subject default', 'forms');
                        $form['message'] = __('Message default', 'forms');
                        $form['template'] = 'top';
                        $form['align'] = 'left';
                        $form['captcha'] = 0;
                    }
                    
                    if (Request::post('submit_save') || Request::post('submit_save_and_exit')) {
                        if (!Security::check(Request::post('csrf')))
                            die('csrf detected!');
                            
                        if (Request::post('name')) $form['name'] = trim(Request::post('name'));
                        if (Request::post('email')) $form['email'] = trim(Request::post('email'));
                        if (Request::post('align')) $form['align'] = trim(Request::post('align'));
                        if (Request::post('button')) $form['button'] = trim(Request::post('button'));
                        if (Request::post('subject')) $form['subject'] = trim(Request::post('subject'));
                        if (Request::post('message')) $form['message'] = trim(Request::post('message'));
                        if (Request::post('template')) $form['template'] = trim(Request::post('template'));
                        if (Request::post('captcha')) $form['captcha'] = 1; else $form['captcha'] = 0;
                        
                        if (empty($form['name'])) $errors['name_empty'] = __('Required field', 'forms');
                        if (empty($form['email'])) $errors['email_empty'] = __('Required field', 'forms');
                          
                        if (count($errors) == 0) {
                        
                            $data = array(
                                'name'=>$form['name'],
                                'email'=>$form['email'],
                                'button'=>$form['button'],
                                'subject'=>$form['subject'],
                                'message'=>$form['message'],
                                'template'=>$form['template'],
                                'captcha'=>$form['captcha'],
                                'align'=>$form['align'],
                            );
                            
                            // Редактирование существующей формы
                            if(isset($form_id)) {
                                $forms->update($form_id, $data);
                                FormsAdmin::refresh($form_id);
                                Notification::set('success', __('Changes successfully saved', 'forms'));
                                
                                if (Request::post('submit_save'))
                                    Request::redirect('index.php?id=forms&form_id='.$form_id.'&action=form');
                                else
                                    Request::redirect('index.php?id=forms');
                                
                            // Добавление новой формы
                            } else {
                                $forms->insert($data);
                                $form_id = $forms->lastId();
                                FormsAdmin::refresh($form_id);
                                
                                Request::redirect('index.php?id=forms&form_id=' . $form_id . '&action=elements');
                            }
                        }
                    }
                    
                    View::factory('forms/views/backend/form')->assign('form', $form)->assign('errors', $errors)->display();
                    break;

                // Удаление формы
                // -------------------------------------
                case "delete":
                    
                    if(Request::get('form_id')) {
                        if (Security::check(Request::get('token'))) {
                            
                            $form_id = (int)Request::get('form_id');
                            $forms->delete($form_id);
                            
                            Notification::set('success', __('Removal was successful', 'forms'));
                            Request::redirect('index.php?id=forms');
                            
                        } else { die('csrf detected!'); }                        
                    }
                    break;
                    
                // Вывод элементов формы
                // -------------------------------------
                case "elements":
                    if(Request::get('form_id')) {
                    
                        $elements = new Table('forms_elements');

                        $form_id = (int)Request::get('form_id');
                        $form = $forms->select('[id='.$form_id.']', null);
                        $elements = $elements->select('[form_id='.$form_id.']', 'all', null, null, 'position');
                        
                        Breadcrumbs::add("index.php?id=forms&form_id={$form_id}&action=form", __('Settings form', 'forms'));
                        Breadcrumbs::add("index.php?id=forms&form_id={$form_id}&action=elements", __('Elements of the form', 'forms'));
                        
                        View::factory('forms/views/backend/elements')
                            ->assign('form', $form)
                            ->assign('elements', $elements)
                            ->display();
                    }
                    break;
                
                // Добавление элемента
                // -------------------------------------
                case "element_add":
                    if(Request::get('form_id')) {
                    
                        $elements = new Table('forms_elements');

                        $form_id = (int)Request::get('form_id');
                        $type = Request::get('type');
                        
                        $position = $elements->count() + 1;
                        
                        $width = (in_array($type, array('subtitle', 'radio', 'checkbox'))) ? '' : 75;
                        
                        if ($type == 'email') {
                            $title = __('Element email', 'forms');
                            $required = 'yes';
                        } elseif ($type == 'name') {
                            $title = __('Element name', 'forms');
                            $required = 'yes';
                        } elseif ($type == 'tel') {
                            $title = __('Element tel', 'forms');
                            $required = 'no';
                        } else {
                            $title = __('New element', 'forms');
                            $required = 'no';
                        }
                        
                        $elements->insert(array(
                            'form_id'=>$form_id,
                            'type'=>$type,
                            'title'=>$title,
                            'comment'=>'',
                            'position'=>$position,
                            'required'=>$required,
                            'values'=>'',
                            'width'=>$width,
                        ));
                        
                        FormsAdmin::refresh($form_id);

                        $element_id = $elements->lastId();
                        
                        // Обновляем структуру файла, где храняться все сообщения
                        // Добавляем туда новое поле
                        //$forms_data = new Table('forms_form' . $form_id);
                        //$forms_data->addField("f{$form_id}_el".$element_id);
                        
                        Request::redirect('index.php?id=forms&element_id=' . $element_id . '&action=element');
                    }
                    break;
                    
                // Редактирование элемента формы
                // -------------------------------------
                case "element":
                    if(Request::get('element_id')) {
                    
                        $elements = new Table('forms_elements');

                        $element_id = (int)Request::get('element_id');
                        
                        $element = $elements->select('[id='.$element_id.']', null);
                        $form = $forms->select('[id='.$element['form_id'].']', null);
                        
                        Breadcrumbs::add("index.php?id=forms&form_id={$element['form_id']}&action=form", __('Settings form', 'forms'));
                        Breadcrumbs::add("index.php?id=forms&form_id={$element['form_id']}&action=elements", __('Elements of the form', 'forms'));
                        Breadcrumbs::add("index.php?id=forms&form_id={$element['form_id']}&action=elements", __('Edit item', 'forms'));
                        
                        if (Request::post('submit_save')) {
                            if (!Security::check(Request::post('csrf')))
                                die('csrf detected!');
                            
                            $width = Request::post('width');
                            $title = trim(Request::post('title'));
                            $values = trim(Request::post('values'));
                            $comment = trim(Request::post('comment'));
                                
                            $required = (Request::post('required')) ? 'yes' : 'no';
                                
                            $values_required = (in_array($element['type'], array('checkbox', 'radio', 'select'))) ? true : false;
                                
                            if (empty($title)) $errors['title_empty'] = __('Required field', 'forms');
                            if (empty($values) and $values_required) $errors['values_empty'] = __('Required field', 'forms');
                                
                            if (count($errors) == 0) {
                                    
                                $elements->update($element_id, array(
                                    'title'=>$title,
                                    'comment'=>$comment,
                                    'required'=>$required,
                                    'values'=>$values,
                                    'width'=>$width,
                                ));
                                    
                                FormsAdmin::refresh($element['form_id']);
                                    
                                Notification::set('success', __('Changes successfully saved', 'forms'));
                                Request::redirect('index.php?id=forms&form_id=' . $form['id'] . '&action=elements');
                            }
                        }
                        
                        if (Request::post('width')) $element['width'] = Request::post('width');
                        if (Request::post('title')) $element['title'] = Request::post('title');
                        if (Request::post('values')) $element['values'] = Request::post('values');
                        if (Request::post('comment')) $element['comment'] = Request::post('comment');
                        if (Request::post('required')) $element['required'] = Request::post('required');
                        
                        View::factory('forms/views/backend/element')
                            ->assign('form', $form)
                            ->assign('errors', $errors)
                            ->assign('element', $element)
                            ->display();
                    }
                    break;
                    
                // Удаление элемента
                // -------------------------------------
                case "element_delete":
                    
                    if (Request::get('element_id')) {
                        if (Security::check(Request::get('token'))) {
                            
                            $elements = new Table('forms_elements');
                            
                            $element_id = (int)Request::get('element_id');
                            $form_id = (int)Request::get('form_id');
                            
                            $elements->delete($element_id);
                            
                            FormsAdmin::refresh($form_id);
                            
                            // Обновляем структуру файла, где храняться все сообщения
                            // Удаляем поле
                            //$forms_data = new Table('forms_form' . $form_id);
                            //$forms_data->deleteField("f{$form_id}_el".$element_id);
                            
                            Notification::set('success', __('Removal was successful', 'forms'));
                            Request::redirect('index.php?id=forms&form_id='.$form_id.'&action=elements');
                            
                        } else { die('csrf detected!'); }
                    }
                    break;
            }
            
        } else {
            $records = $forms->select();
            View::factory('forms/views/backend/forms')->assign('records', $records)->display();
        }
    }
    
    /**
     * Ajax
     */
    public static function ajax() {

        // Изменение порядка вывода элементов
        // --------------------------------------
        if (isset($_POST['forms_elements'])) {
            $elements = new Table('forms_elements');

            foreach ($_POST['forms_elements'] as $position=>$element_id)
                if (intval($element_id) > 0)
                    $elements->update($element_id, array('position'=>$position));
                    
            FormsAdmin::refresh(intval($_GET['form_id']));
            exit(__('Changes successfully saved', 'forms'));
        }
        
        // Изменение флажка "обазательный элемент"
        if (Request::post('forms_element_required_check')) {
            $elements = new Table('forms_elements');
            $elements->update((int)Request::post('element_id'), array('required' => Request::post('val')));
            FormsAdmin::refresh((int)Request::post('form_id'));
            exit();
        }
        
        // Изменение ширины элементов
        if (Request::post('forms_element_width_change')) {
            $elements = new Table('forms_elements');
            $elements->update((int)Request::post('element_id'), array('width' => (int)Request::post('width')));
            FormsAdmin::refresh((int)Request::post('form_id'));
            exit();
        }
        
        // Убирам фразу "Установить демо-данные?"
        if (Request::post('forms_demo_msg_close')) {
            Option::update('forms-demo-msg', 0);
            exit();
        }
    }
    
    /** 
     * Обновление шаблона формы
     */
    public static function refresh($form_id) {
        
        $forms = new Table('forms');
        $elements = new Table('forms_elements');
        
        $path = STORAGE . DS  . 'forms' . DS;
        
        $form = $forms->select('[id='.$form_id.']', null);
        $items = $elements->select('[form_id='.$form_id.']', 'all', null, null, 'position');
        
        $html = "<div class=\"forms-table forms-".$form['template']."\">\n\n";
        
        if (count($items) > 0) {
            foreach ($items as $item) {
            
                // Если это подзаголовок
                if ($item['type'] == 'subtitle') {
                    $html.= (
                        "<div class=\"forms-item forms-nolabel\">\n".
                            "<div class=\"forms-label\"></div>\n".
                            "<div class=\"forms-field forms-subtitle\">".
                                $item['title']."<div class=\"forms-comment\">".$item['comment']."</div>".
                            "</div>\n".
                        "</div>\n"
                    );
                
                // Если это элемент формы
                } else {
                    if ($form['template'] == 'inside') {
                        $html.= (
                            "<div class=\"forms-item\">\n".
                                "<div class=\"forms-field\">".FormsAdmin::elementHtml($item, $item['title'])."</div>\n".
                            "</div>\n"
                        );
                    } else {
                        $html.= (
                            "<div class=\"forms-item\">\n".
                                "<div class=\"forms-label ".(($item['required'] == 'yes') ? 'forms-required' : '')."\">".
                                    "<div>".$item['title']."</div><span class=\"forms-comment\">".$item['comment']."</span>".
                                "</div>\n".
                                "<div class=\"forms-field\">".FormsAdmin::elementHtml($item)."</div>\n".
                            "</div>\n"
                        );
                    }
                }
            }
        }
        
        // Если установлен плагин "капча", то выводим ее
        if (Option::get('captcha_installed') == 'true' and $form['captcha'] == 1) {
            $html.= (
                "<div class=\"forms-item\">\n".
                    "<div class=\"forms-label\">".__('Captcha', 'forms')."</div>\n".
                    "<div class=\"forms-field\"><input type=\"text\" name=\"captcha\" class=\"forms-width25\"/><?php CryptCaptcha::draw();?></div>\n".
                "</div>\n"
            );
        }
        
        $align = (empty($form['align'])) ? 'left' : $form['align'];
        
        $html.= (
            "<div class=\"forms-item forms-nolabel\">\n".
                "<div class=\"forms-label\"></div>\n".
                "<div class=\"forms-field forms-{$align}\"><button type=\"submit\" class=\"btn btn-default\">{$form['button']}</button></div>\n".
            "</div>\n"
        );
            
        $html.= "</div>";
        
        File::setContent($path.$form_id.'.form.php', $html);
    }
    
    /**
     * Element data
     */
    public static function elementHtml($data, $placeholder = '') {
        $html = '';
        
        $required = (($data['required'] == 'yes') ? ' required' : '');
        
        switch ($data['type']) {
            
            case "tel":
            case "email":
            case "name":
            case "text":
                if ($data['type'] == 'name') $data['type'] = 'text';
                $html = '<input type="'.$data['type'].'" name="forms_item'.$data['id'].'"'.$required.' placeholder="'.$placeholder.'" class="form-control forms-width'.$data['width'].'"/>';
                break;
            
            case "textarea":
                $html = '<textarea name="forms_item'.$data['id'].'"'.$required.' placeholder="'.$placeholder.'" class="form-control forms-width'.$data['width'].'"></textarea>';
                break;
                
            case "radio":
            case "checkbox":
                $values = explode("\n", $data['values']);
                
                // We checkbox multiple responses
                $name = ($data['type'] == 'checkbox') ? 'forms_many'.$data['id'].'[]' : 'forms_item'.$data['id'];
                
                if (count($values) > 0) {
                    foreach ($values as $val) {
                        $html.= '<div><label><input type="'.$data['type'].'" name="'.$name.'"'.$required.' value="'.trim($val).'"/> '.trim($val).'</label></div>';
                    }
                }
                break;
                
            case "select":
                $values = explode("\n", $data['values']);

                if (count($values) > 0) {
                    $html.= '<select name="forms_item'.$data['id'].'"'.$required.' class="form-control forms-width'.$data['width'].'">';
                    if (empty($required)) $html.= '<option>-</option>';
                    foreach ($values as $val) { $html.= '<option value="'.trim($val).'"/>'.trim($val).'</option>'; }
                    $html.= '</select>';
                }
                break;
        }
        return $html;
    }
}
