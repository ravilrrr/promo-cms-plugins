<?php

/**
 *  Guestbook plugin
 *
 *  @package Promo CMS
 *  @subpackage Plugins
 *  @author Yudin Evgeniy / JINN
 *  @copyright 2013-2015 Yudin Evgeniy / JINN
 *  @version 1.0.0
 *
 */

// Register plugin
Plugin::register( __FILE__,
                __('Guestbook', 'guestbook'),
                __('Guestbook plugin for Promo CMS', 'guestbook'),
                '1.0.0',
                'JINN',
                'http://cms.promo360.ru',
                'guestbook');

// Load Guestbook Admin for Editor and Admin
if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {
    Plugin::admin('guestbook');
}

Stylesheet::add('plugins/guestbook/lib/site.css', 'frontend', 11);
Stylesheet::add('plugins/guestbook/lib/admin.css', 'backend', 11);

Javascript::add('plugins/guestbook/lib/site.js', 'frontend', 11);
Javascript::add('plugins/guestbook/lib/admin.js', 'backend', 11);
    
Shortcode::add('guestbook', 'Guestbook::shortcode');

Action::add('frontend_pre_render', 'Guestbook::ajax');

class Guestbook extends Frontend
{

    public static $guestbook = null;
    public static $content_messages = '';
    public static $content_footer = '';
    public static $content_form = '';
    
    public static function main()
    {
        Guestbook::$guestbook = new Table('guestbook');

        if (Uri::segment(1) == 1) Request::redirect(Site::url().'/guestbook');
        elseif (Uri::segment(1)) Guestbook::all(Uri::segment(1));
        else Guestbook::all();
    }
    
    public static function all($page = 1)
    {
    
        $query = (Option::get('guestbook_check') == 'yes') ? '[check=1]' : '';
        
        $records_all = Guestbook::$guestbook->select($query, 'all', null, null, 'date', 'DESC');
        
        // Paginator
        // ---------------------------------
        $count = count($records_all);
        $limit = Option::get('guestbook_limit');
        $pages = ceil($count/$limit);
        $start = ($page-1)*$limit;
                
        if ($count > 0) $records = array_slice($records_all, $start, $limit);  
        else $records = array();
        
        if (($page < 1 or $page > $pages) and $count > 0) 
            Request::redirect(Site::url().'/guestbook');
        
        Guestbook::$content_form = View::factory('guestbook/views/frontend/form')->render();
        
        Guestbook::$content_messages = View::factory('guestbook/views/frontend/messages')
            ->assign('records', $records)
            ->assign('answer_show', true)
            ->render();
            
        Guestbook::$content_footer = View::factory('guestbook/views/frontend/footer')
            ->assign('current_page', $page)
            ->assign('pages_count', $pages)
            ->render();
    }
    
    /**
     *  Shortcode
     *
     *  Example:
     *  Count: {guestbook show="count"}
     *  Last 3: {guestbook show="last" count="3"}
     *  Random 3: {guestbook show="random" count="3"}
     *  Last important: {guestbook show="last" count="3" label="important"}
     *  Random important: {guestbook show="random" count="3" label="important"}
     *  Show answer: {guestbook show="last" count="3" answer="true"}
     */
    public static function shortcode($attributes) 
    {
        extract($attributes);
        
        $answer = (isset($answer))  ? true : false;
        $show   = (isset($show))    ? $show : 'last';
        $count  = (isset($count))   ? (int)$count : 3;
        $label  = (isset($label))   ? 'important' : null;
        
        switch ($show) {
            case 'count':   return Guestbook::count(); break;
            case 'last':    return Guestbook::show('last', $count, $label, $answer); break;
            case 'random':  return Guestbook::show('random', $count, $label, $answer); break;
        }
    }
    
    /** 
     *  Example: <?php echo Guestbook::count(); ?>
     */
    public static function count() 
    {
        Guestbook::$guestbook = new Table('guestbook');
        return Guestbook::$guestbook->count();
    }
    
    /**
     *  Example:
     *
     *  <?php echo Guestbook::show('last'); ?>
     *  <?php echo Guestbook::show('last', 1); ?>
     *  <?php echo Guestbook::show('last', 10, 'important'); ?>
     *  <?php echo Guestbook::show('last', 3, null, true); ?>
     *
     *  <?php echo Guestbook::show('random'); ?>
     *  <?php echo Guestbook::show('random', 1); ?>
     *  <?php echo Guestbook::show('random', 10, 'important'); ?>
     *  <?php echo Guestbook::show('random', 3, null, true); ?>
     *
     *  what: last, random
     *  label: important
     */
    public static function show($what='last', $count=3, $label=null, $answer=false)
    {
        Guestbook::$guestbook = new Table('guestbook');
        
        $query = ($label == 'important') ? '[important=1]' : '';
        
        $records_all = Guestbook::$guestbook->select($query, 'all', null, null, 'date', 'DESC');
        
        if ($what == 'random')
            $records = Guestbook::array_random($records_all, $count);
        else
            $records = array_slice($records_all, 0, $count);

        $messages = View::factory('guestbook/views/frontend/messages')
            ->assign('records', $records)
            ->assign('answer_show', $answer)
            ->render();
            
        return $messages;
    }
    
    public static function ajax() 
    {
        // Add guestbook
        // ---------------------------------------------
        if (Request::post('message_guestbook')) {
            if (Security::check(Request::post('csrf'))) {
                if (Request::post('spam_hello') == '') {
                    
                    $error = '';
                    
                    if (Option::get('captcha_installed') == 'true' and ! CryptCaptcha::check(Request::post('captcha'))) {
                        $error = __('Captcha code is wrong', 'guestbook');
                    }
                    
                    if (trim(Request::post('message_guestbook')) == '') {
                        $error = __('Message empty', 'guestbook');
                    }
                    
                    if (Session::exists('guestbook_time')) {
                        $second = time() - Session::get('guestbook_time');
                        if ($second < Option::get('guestbook_time')) {
                            $error = __('With the last of your message is too early, try a little later.', 'guestbook');
                        }
                    }
                        
                    $name = (Request::post('name')) ? Request::post('name') : __('Guest', 'guestbook');
                
                    if (empty($error)) {
                    
                        $data = array(
                            'name' => $name, 
                            'date' => time(), 
                            'message' => Request::post('message_guestbook'), 
                            'answer' => '', 
                            'check' => 0,
                        );
                
                        Guestbook::$guestbook->insert($data);
                        
                        Session::set('guestbook_time', time());
                        
                        if (Option::get('guestbook_check') == 'yes') 
                            $data['answer'] = __('Notice of the pre-moderation', 'guestbook');
                
                        $array['template'] = View::factory('guestbook/views/frontend/messages')
                            ->assign('records', array($data))
                            ->assign('answer_show', true)
                            ->render();
                            
                        Guestbook::sendMail($data);
                
                        $array['result'] = 'success';
                        
                    } else 
                        $array['result'] = $error;
                } else 
                    $array['result'] = 'You are a robot';
            } else 
                $array['result'] = 'csrf detected!';
         
            exit(json_encode($array));
        }
    }

    public static function content()
    {
        return Guestbook::$content_form . Guestbook::$content_messages . Guestbook::$content_footer;
    }
    
    public static function title()
    {
        return __('Guestbook', 'guestbook');
    }
    
    public static function template()
    {
        return Option::get('guestbook_template');
    }
    
    public static function sendMail($data)
    {
        $emailTo = Option::get('guestbook_email');
        $emailFrom = Option::get('system_email');
        
        if ($emailTo != '' and Option::get('guestbook_double') == 'yes') {
            
            $page_current = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        
            $msg = '<b>' . $data['name'] . '</b><br/>';
            $msg.= $data['message'] . '<br/><br/>';
            $msg.= '<a href="'.$page_current.'">'.$page_current.'</a>';
        
            $emailToArray = explode(',', $emailTo);

            $mail = new PHPMailer();
            $mail->CharSet = 'utf-8';
            $mail->ContentType = 'text/html';
            $mail->SetFrom($emailFrom, Site::url());
            $mail->AddReplyTo($emailFrom, Site::url());
                        
            if (count($emailToArray) > 1) {
                foreach ($emailToArray as $e)
                    $mail->AddAddress(trim($e));
            } else {
                $mail->AddAddress($emailTo);
            }

            $mail->Subject = __('A new guestbook on the site', 'guestbook');
            $mail->MsgHTML($msg);
            $mail->Send();
        }
    }
    
    /**
     *  current page
     *  pages all
     *  site_url
     *  limit pages
     */
    public static function paginator($current, $pages, $site_url, $limit_pages=10) 
    {
            
        if ($pages > 1) {
            
            // pages count > limit pages
            if ($pages > $limit_pages) {
                $start = ($current <= 6) ? 1 : $current-3;
                $finish = (($pages-$limit_pages) > $current) ? ($start + $limit_pages - 1) : $pages;
            } else {
                $start = 1;
                $finish = $pages;
            }

            // pages title
            echo '<strong>'.__('Pages:', 'guestbook').'</strong> &nbsp; < ';
                
            // prev
            if($current==1){ echo __('Prev', 'guestbook');} 
            else { echo '<a href="'.$site_url.($current-1).'">'.__('Prev', 'guestbook').'</a> '; } echo '&nbsp; ';
                
            // next
            if($current==$pages){ echo __('Next', 'guestbook'); }
            else { echo '<a href="'.$site_url.($current+1).'">'.__('Next', 'guestbook').'</a> '; } echo ' > ';
    
            // pages list
            echo '<div class="guestbook-page">';
                
                if (($pages > $limit_pages) and ($current > 6)) {
                    echo '<a href="'.$site_url.'1">1</a>';
                    echo '<span>...</span>'; 
                }
                
                for ($i = $start; $i <= $finish; $i++) {
                    $class = ($i == $current) ? ' class="current"' : '';
                    echo '<a href="'.$site_url.$i.'"'.$class.'>'.$i.'</a>'; 
                }
                
                if (($pages > $limit_pages) and ($current < ($pages - $limit_pages))) {
                    echo '<span>...</span>'; 
                    echo '<a href="'.$site_url.$pages.'">'.$pages.'</a>';
                }
            echo '</div>';
        }
    }
    
    /**
     *  Array random
     *  http://www.php.net/manual/ru/function.array-rand.php#107813
     */
    public static function array_random($arr, $num = 1) {
        shuffle($arr);
     
        $r = array();
        for ($i = 0; $i < $num; $i++) {
            $r[] = $arr[$i];
        }
        //return $num == 1 ? $r[0] : $r;
        return $r;
    }
    
    /** 
     *  Get date: today, yesterday, date
     *  floor((time()-$date))/86400) - is not working as it should, so I write shit code
     */
    public static function getdate($date, $format='d.m.Y') {
        
        $date_full = Date::format($date, $format);
        $date_day = Date::format($date, 'd');
        $date_my = Date::format($date, 'm.Y');
        
        $today_full = date($format);
        $today_day = date('d');
        $today_my = date('m.Y');
        
        if ($date_full == $today_full) return __('Today', 'guestbook');
        elseif ($date_my == $today_my and $date_day == ($today_day - 1)) return __('Yesterday', 'guestbook');
        else return $date_full;
    }
}
