<?php

/**
 *  Forms plugin
 *
 *  @package Promo CMS
 *  @subpackage Plugins
 *  @author Yudin Evgeniy / JINN
 *  @copyright 2014-2015 Yudin Evgeniy / JINN
 *  @version 1.0.0
 *
 */

// Register plugin
Plugin::register( __FILE__,
                __('Forms', 'forms'),
                __('Forms plugin for Promo CMS', 'forms'),
                '1.0.0',
                'JINN',
                'http://cms.promo360.ru');

// Load Forms Admin for Editor and Admin
if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {
    Plugin::admin('forms');
}

// Frontend lib
Stylesheet::add('plugins/forms/lib/frontend/style.css', 'frontend', 11);
Javascript::add('plugins/forms/lib/frontend/script.js', 'frontend', 11);

// Backend lib
Stylesheet::add('plugins/forms/lib/backend/style.css', 'backend', 11);
Javascript::add('plugins/forms/lib/backend/jquery.tablednd.0.8.min.js', 'backend', 11);
Javascript::add('plugins/forms/lib/backend/script.js', 'backend', 12);
    
// Shortcode
Shortcode::add('forms', 'Forms::shortcode');

// Ajax
Action::add('frontend_pre_render','Forms::ajax');

class Forms
{
    /**
     *  Shortcode
     *
     *  Example: 
     *  id - required
     *  {forms id="23"}
     *  {forms id="23" to="your@mail.ru"}
     *  {forms id="23" to="your@mail.ru" title="product name ¹000"}
     */
    public static function shortcode($attributes)
    {
        extract($attributes);
        
        if (!isset($title)) $title = '';
        if (!isset($to)) $to = '';
        
        return Forms::get($id, $title, $to);
    }
    
    /**
     *  PHP code
     *
     *  Example: 
     *  <?=Forms::get(4)?>
     *  <?=Forms::get(4, 'Product name ¹000')?>
     *  <?=Forms::get(4, '', 'your@mail.ru')?>
     */
    public static function get($id, $title='', $to='')
    {
        // Recipient
        if (!empty($to)) Session::set('forms_email_to'.$id, $to);
        
        $path = STORAGE . DS . 'forms' . DS . $id . '.form.php';

        if (File::exists($path)) {
            ob_start();
            echo '<form method="post" onsubmit="formsSend(this); return false;">';
            echo '<div class="forms-hide"><input type="text" name="antispam" value=""/></div>';
            echo '<input type="hidden" name="form_id" value="'.$id.'"/>';
            echo '<input type="hidden" name="title" value="'.$title.'"/>';
            echo Form::hidden('csrf', Security::token());
            include $path;
            echo '</form>';
            $contents = ob_get_contents();
            ob_end_clean();
 
            return Filter::apply('content', Text::toHtml($contents));
        }
    }
    
    /**
     *  Ajax
     */
    public static function ajax()
    {
        // Send mail
        // --------------------------------------
        if (isset($_POST['antispam'])) {
            
            $error = '';
            
            $msg = Request::post('title');
            if (!empty($msg)) $msg.= '<br/>';
            
            $form_id = (int)Request::post('form_id');
                
            $page_current = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
            
            if (Security::check(Request::post('csrf'))) {
                if (Request::post('antispam') == '') {
                
                    if ($form_id > 0) {
                
                        $forms = new Table('forms');
                        $elements = new Table('forms_elements');
                
                        $form = $forms->select('[id='.$form_id.']', null);
                        $items = $elements->select('[form_id='.$form_id.']', 'all', null, null, 'position');
                    
                        // Recipient
                        $emailTo = (Session::get('forms_email_to'.$form_id)) ? Session::get('forms_email_to'.$form_id) : $form['email'];
                    
                        // Sender
                        $emailFrom = Option::get('system_email');
                        $nameFrom = '';
                    
                        if ($form['captcha'] == 1) {
                            if (Option::get('captcha_installed') == 'true' && ! CryptCaptcha::check(Request::post('captcha'))) {
                                $error = __('Captcha code is wrong', 'forms');
                            }
                        }
                    
                        if (count($items) > 0 && empty($error)) {
                            foreach ($items as $item) {
                                $val = Request::post('forms_item'.$item['id']);
                                $many = Request::post('forms_many'.$item['id']);
                                
                                if (empty($val)) {
                            
                                    if ($item['required'] == 'yes' && $item['type'] != 'checkbox') {
                                        $error = __('Please fill in all mandatory fields', 'forms');
                                        break;
                                    }
                                
                                    if ($item['type'] == 'checkbox' && !empty($many)) {
                                        $val = implode(', ', $many);
                                    } else {
                                        $val = '-';
                                    }
                                
                                } else {
                            
                                    if ($item['type'] == 'email') {
                                        if (Valid::email($val)) {
                                            $emailFrom = $val;
                                        } else {
                                            $error = __('Please enter a valid e-mail', 'forms');
                                            break;
                                        }
                                    } elseif ($item['type'] == 'name') {
                                        $nameFrom = $val;
                                    }
                                }
                            
                                $msg.= '<b>'.$item['title'].'</b> '.$val.'<br/>';
                            }
                        }
                    
                        $msg.= '<br/>'.__('Message sent with the form on the page', 'forms').' <a href="'.$page_current.'">'.$page_current.'</a>';
                    
                        if (empty($error)) {
                    
                            $emailToArray = explode(',', $emailTo);
                        
                            $mail = new PHPMailer();
                            $mail->CharSet = 'utf-8';
                            $mail->ContentType = 'text/html';
                            $mail->SetFrom($emailFrom, $nameFrom);
                            $mail->AddReplyTo($emailFrom, $nameFrom);
                        
                            if (count($emailToArray) > 1) {
                                foreach ($emailToArray as $e)
                                    $mail->AddAddress(trim($e));
                            } else {
                                $mail->AddAddress($emailTo);
                            }
                        
                            $mail->Subject = $form['subject'];
                            $mail->MsgHTML($msg);
                            $mail->Send();
        
                            $array['message'] = $form['message'];
                            $array['result'] = 'success';
                        
                        } else
                            $array['result'] = $error;
                    } else
                        $array['result'] = 'Do not have a form';           
                } else
                    $array['result'] = 'You are a robot';
            } else 
                $array['result'] = 'csrf detected!';
                
            exit(json_encode($array));
        }
    }
}
