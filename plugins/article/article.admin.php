<?php 

    Navigation::add(__('Article', 'article'), 'content', 'article', 10);
    
    Action::add('admin_themes_extra_index_template_actions','ArticleAdmin::formComponent');
    Action::add('admin_themes_extra_actions','ArticleAdmin::formComponentSave');
    Action::add('admin_header','ArticleAdmin::_header', 9);
    
    class ArticleAdmin extends Backend {
        
        public static function _header() {
            if (isset(Plugin::$plugins['markitup'])) {
                if (Request::get('id') == 'article') {
                    echo "<script>$(document).ready(function(){mySettings.markupSet.push({name:'Cut', replaceWith:'{cut}', className:'article-cut' });});</script>";
                }
            }
        }
        
        public static function main() {

            $site_url = Option::get('siteurl');
            $width = (int)Option::get('article_w');
            $errors = array();
            $status_array = array('published' => __('Published', 'article'), 'draft' => __('Draft', 'article'));
            
            $imgdir = ROOT . DS . 'public' . DS . 'article' . DS . 'images' . DS;
            $imgdefault = ROOT . DS . 'public' . DS . 'article' . DS . 'default.jpg';
            $imgdefault_url = $site_url.'/public/article/default.jpg';
            
            Breadcrumbs::add('index.php?id=article', __('Article', 'article'));
            
            $article = new Table('article'); 
            $users = new Table('users');  
            
            $user = $users->select('[id='.Session::get('user_id').']', null);

            $user['firstname'] = Html::toText($user['firstname']);
            $user['lastname']  = Html::toText($user['lastname']);

            if (isset($user['firstname']) && trim($user['firstname']) !== '') {
                if (trim($user['lastname']) !== '') $lastname = ' '.$user['lastname']; else $lastname = '';
                $author = $user['firstname'] . $lastname;
            } else {
                $author = Session::get('user_login');
            }
            
            Action::run('admin_article_extra_actions');
            
            if (Request::get('action')) {
                switch (Request::get('action')) {
                    
                    case "settings":
                        
                        if (Request::post('article_submit_settings_cancel')) {
                            Request::redirect('index.php?id=article');
                        }
                        
                        // save settings
                        if (Request::post('article_submit_settings')) {
                            if (Security::check(Request::post('csrf'))) {
                                Option::update(array(
                                    'article_limit'  => (int)Request::post('limit'), 
                                    'article_limit_admin' => (int)Request::post('limit_admin'),
                                    'article_w' => (int)Request::post('width')
                                ));
                                
                                Notification::set('success', __('Your changes have been saved', 'article'));
                                                
                                Request::redirect('index.php?id=article');
                            } else { die('csrf detected!'); }
                        }
                        
                        // upload default image
                        if (Request::post('article_submit_image')) {
                            if (Security::check(Request::post('csrf'))) { 
                                if ($_FILES['file']) {
                                    if($_FILES['file']['type'] == 'image/jpeg' ||
                                        $_FILES['file']['type'] == 'image/png' ||
                                        $_FILES['file']['type'] == 'image/gif') {
                                        
                                        $img  = Image::factory($_FILES['file']['tmp_name']);
                                        $img->resize($width, $width, Image::WIDTH)->save($imgdefault);
                                    }
                                }
                                Request::redirect('index.php?id=article&action=settings');
                            } else { die('csrf detected!'); }
                        }
                        
                        // delete image default
                        if (Request::get('delete_img') and Request::get('delete_img') == 'default') {
                            if (Security::check(Request::get('token'))) {
                                unlink($imgdefault);
                                Request::redirect('index.php?id=article&action=settings');
                            }
                        }
                        
                        Breadcrumbs::add('index.php?id=article&action=settings', __('Settings', 'article'));
            
                        View::factory('article/views/backend/settings')
                            ->assign('imgdefault', $imgdefault)
                            ->assign('imgdefault_url', $imgdefault_url)
                            ->display();
                        Action::run('admin_article_extra_settings_template'); 
                        break;
                        
                    case "edit":
                        
                        if(Request::get('article_id')) {
                            
                            // delete image article
                            if (Request::get('delete_img')) {
                                if (Security::check(Request::get('token'))) {
                                    $id = (int)Request::get('article_id');
                                    unlink($imgdir . Request::get('delete_img'));
                                    $article->update($id, array('img'=>''));
                                    Request::redirect('index.php?id=article&action=edit&article_id='.$id);
                                }
                            }
                        
                            if (Request::post('edit_article') || Request::post('edit_article_and_exit')) {
                                if (Security::check(Request::post('csrf'))) {
                                
                                    if (trim(Request::post('article_name')) == '') $errors['article_empty_name'] = __('Required field', 'article');
                                
                                    if (trim(Request::post('article_slug')) == '') $article_slug = trim(Request::post('article_name')); 
                                    else $article_slug = trim(Request::post('article_slug'));

                                    if (Valid::date(Request::post('article_date'))) $date = strtotime(Request::post('article_date'));
                                    else $date = time();
                                    
                                    // paranoia
                                    if (Request::post('article_id')) {
                                        if (Request::post('article_id') == Request::get('article_id')) {
                                            $id = (int)Request::post('article_id');
                                        } else {
                                            $errors['article_empty_id'] = 'error: post id != get id';
                                        }
                                    } else {
                                        $errors['article_empty_id'] = 'error: empty id';
                                    }
                                    
                                    if (count($errors) == 0) {
                                    
                                        $imgname = Request::post('article_img');
                                
                                        if ($_FILES['file']) {
                                            if($_FILES['file']['type'] == 'image/jpeg' ||
                                                $_FILES['file']['type'] == 'image/png' ||
                                                $_FILES['file']['type'] == 'image/gif') {
                                        
                                                $imgname = Text::random('alnum', 10).'.jpg';
                                        
                                                $img = Image::factory($_FILES['file']['tmp_name']);
                                                $img->resize($width, $width, Image::WIDTH)->save($imgdir . $imgname);
                                            }
                                        }
                                
                                        $data = array(
                                            'name'         => Request::post('article_name'),
                                            'title'        => Request::post('article_title'), 
                                            'h1'           => Request::post('article_h1'), 
                                            'description'  => Request::post('article_description'),
                                            'keywords'     => Request::post('article_keywords'),
                                            'slug'         => Security::safeName($article_slug, '-', true),
                                            'date'         => $date,
                                            'author'       => $author,
                                            'status'       => Request::post('status'),
                                            'img'          => $imgname
                                        );
                                                            
                                        if($article->updateWhere('[id='.$id.']', $data)) {
                                            File::setContent(STORAGE . DS . 'article' . DS . $id . '.article.txt', XML::safe(Request::post('editor')));
                                            Notification::set('success', __('Your changes to the article <i>:article</i> have been saved.', 'article', 
                                                array(':article' => Security::safeName(Request::post('article_name'), '-', true))));
                                        }

                                        Action::run('admin_article_action_edit');   
                   
                                        if (Request::post('edit_article_and_exit')) {
                                            Request::redirect('index.php?id=article');
                                        } else {
                                            Request::redirect('index.php?id=article&action=edit&article_id='.$id); 
                                        } 
                                    }
                                }
                            }
                            
                            $id = (int)Request::get('article_id');
                            $data = $article->select('[id="'.$id.'"]', null);
                            
                            if($data) {
                                
                                $article_content = File::getContent(STORAGE . DS . 'article' . DS . $id . '.article.txt');
                                
                                $post_name          = (Request::post('article_name'))        ? Request::post('article_name')        : $data['name'];
                                $post_slug          = (Request::post('article_slug'))        ? Request::post('article_slug')        : $data['slug'];
                                $post_h1            = (Request::post('article_h1'))          ? Request::post('article_h1')          : $data['h1'];
                                $post_title         = (Request::post('article_title'))       ? Request::post('article_title')       : $data['title'];
                                $post_keywords      = (Request::post('article_keywords'))    ? Request::post('article_keywords')    : $data['keywords'];
                                $post_description   = (Request::post('article_description')) ? Request::post('article_description') : $data['description'];
                                $status             = (Request::post('status'))           ? Request::post('status')           : $data['status'];
                                $date               = (Request::post('article_date'))        ? Request::post('article_date')        : $data['date']; $post_content       = (Request::post('editor'))           ? Request::post('editor') : Text::toHtml($article_content);
                                
                                $date = Date::format($date, 'Y-m-d H:i:s');
                                
                                Notification::setNow('article', 'article');
                                
                                Breadcrumbs::add('index.php?id=article', __('Edit article', 'article'));
                                
                                View::factory('article/views/backend/edit')
                                    ->assign('article_id', $id)
                                    ->assign('post_name', $post_name)
                                    ->assign('post_slug', $post_slug)
                                    ->assign('post_h1', $post_h1)
                                    ->assign('post_title', $post_title)
                                    ->assign('post_description', $post_description)
                                    ->assign('post_keywords', $post_keywords)
                                    ->assign('post_content', $post_content)
                                    ->assign('status_array', $status_array)
                                    ->assign('status', $status)
                                    ->assign('date', $date)
                                    ->assign('errors', $errors) 
                                    ->assign('imgdir', $imgdir)
                                    ->assign('imgname', $data['img'])
                                    ->assign('imgurl', $site_url.'/public/article/images/'.$data['img'])
                                    ->display();
                            }
                        }
                        break;
                        
                    case "add":
                    
                        if (Request::post('add_article') || Request::post('add_article_and_exit')) {
                            if (Security::check(Request::post('csrf'))) {
                                
                                if (trim(Request::post('article_name')) == '') $errors['article_empty_name'] = __('Required field', 'article');
                                
                                if (trim(Request::post('article_slug')) == '') $article_slug = trim(Request::post('article_name')); 
                                else $article_slug = trim(Request::post('article_slug'));

                                if (Valid::date(Request::post('article_date'))) $date = strtotime(Request::post('article_date'));
                                else $date = time();
                                
                                if (count($errors) == 0) {
                                    
                                    $imgname = '';
                                
                                    if ($_FILES['file']) {
                                        if($_FILES['file']['type'] == 'image/jpeg' ||
                                            $_FILES['file']['type'] == 'image/png' ||
                                            $_FILES['file']['type'] == 'image/gif') {
                                        
                                            $imgname = Text::random('alnum', 10).'.jpg';
                                        
                                            $img = Image::factory($_FILES['file']['tmp_name']);
                                            $img->resize($width, $width, Image::WIDTH)->save($imgdir . $imgname);
                                        }
                                    }
                                
                                    $data = array(
                                        'name'         => Request::post('article_name'),
                                        'title'        => Request::post('article_title'), 
                                        'h1'           => Request::post('article_h1'), 
                                        'description'  => Request::post('article_description'),
                                        'keywords'     => Request::post('article_keywords'),
                                        'slug'         => Security::safeName($article_slug, '-', true),
                                        'date'         => $date,
                                        'author'       => $author,
                                        'status'       => Request::post('status'),
                                        'img'          => $imgname
                                    );
                                                            
                                    if($article->insert($data)) {
                                                           
                                        $last_id = $article->lastId();

                                        File::setContent(STORAGE . DS . 'article' . DS . $last_id . '.article.txt', XML::safe(Request::post('editor')));
                                        
                                        Notification::set('success', __('Your changes to the article <i>:article</i> have been saved.', 'article', array(':article' => Security::safeName(Request::post('article_name'), '-', true))));
                                    }

                                    Action::run('admin_article_action_add');   
                   
                                    if (Request::post('add_article_and_exit')) {
                                        Request::redirect('index.php?id=article');
                                    } else {
                                        Request::redirect('index.php?id=article&action=edit&article_id='.$last_id); 
                                    } 
                                }
                            } else { die('csrf detected!'); }
                        }
                        
                        $post_name          = (Request::post('article_name'))        ? Request::post('article_name')        : '';
                        $post_slug          = (Request::post('article_slug'))        ? Request::post('article_slug')        : '';
                        $post_h1            = (Request::post('article_h1'))          ? Request::post('article_h1')          : '';
                        $post_title         = (Request::post('article_title'))       ? Request::post('article_title')       : '';
                        $post_keywords      = (Request::post('article_keywords'))    ? Request::post('article_keywords')    : '';
                        $post_description   = (Request::post('article_description')) ? Request::post('article_description') : '';
                        $post_content       = (Request::post('editor'))           ? Request::post('editor')           : '';
                        
                        $date = Date::format(time(), 'Y-m-d H:i:s');
                        Notification::setNow('article', 'article');
                        
                        Breadcrumbs::add('index.php?id=article&action=add', __('New article', 'article'));

                        View::factory('article/views/backend/add')
                            ->assign('post_name', $post_name)
                            ->assign('post_slug', $post_slug)
                            ->assign('post_h1', $post_h1)
                            ->assign('post_title', $post_title)
                            ->assign('post_description', $post_description)
                            ->assign('post_keywords', $post_keywords)
                            ->assign('post_content', $post_content)
                            ->assign('status_array', $status_array)
                            ->assign('date', $date)
                            ->assign('errors', $errors)
                            ->display();
                        break;

                    case "delete":
                    
                        if (Request::get('article_id')) {
                            if (Security::check(Request::get('token'))) {
                                $id = (int)Request::get('article_id');
                                
                                $data = $article->select('[id='.$id.']', null);
                                
                                if ($article->deleteWhere('[id='.$id.']')) {
                                    File::delete(STORAGE . DS . 'article' . DS . $id . '.article.txt');
                                    Notification::set('success', __('Article <i>:article</i> deleted', 'article', 
                                        array(':article' => Html::toText($data['name']))));
                                }

                                Action::run('admin_pages_action_delete');
                                Request::redirect('index.php?id=article');

                            } else { die('csrf detected!'); }
                        } 
                        break;
                }
                
            } else {
                $limit = Option::get('article_limit_admin');
                $records_all = $article->select(null, 'all', null, array('name', 'slug', 'status', 'date', 'author'));
                $count_article = count($records_all);
                $pages = ceil($count_article/$limit);
            
                $page = (Request::get('page')) ? (int)Request::get('page') : 1;
                $sort = (Request::get('sort')) ? (string)Request::get('sort') : 'date';
                $order = (Request::get('order') and Request::get('order')=='ASC') ? 'ASC' : 'DESC';
                
                if ($page < 1) { $page = 1; } 
                elseif ($page > $pages) { $page = $pages; }
            
                $start = ($page-1)*$limit;

                $records_sort = Arr::subvalSort($records_all, $sort, $order);
                if($count_article>0) $records = array_slice($records_sort, $start, $limit);
                else $records = array();

                View::factory('article/views/backend/index')
                    ->assign('article_list', $records)
                    ->assign('site_url', $site_url)
                    ->assign('status_array', $status_array)
                    ->assign('current_page', $page)
                    ->assign('pages_count', $pages)
                    ->assign('sort', $sort)
                    ->assign('order', $order)
                    ->display();
            }
	    }

        /**
         * Form Component Save
         */
        public static function formComponentSave() {
            if (Request::post('article_component_save')) {
                if (Security::check(Request::post('csrf'))) {
                    Option::update('article_template', Request::post('article_form_template'));
                    Request::redirect('index.php?id=themes');
                }
            }
        }

        /**
         * Form Component
         */
        public static function formComponent() {

            $_templates = Themes::getTemplates();
            foreach($_templates as $template) {
                $t = basename($template, '.template.php');
                $templates[$t] = $t;
            }
           
            echo (
                Form::open().
                Form::hidden('csrf', Security::token()).
                Form::label('article_form_template', __('Article template', 'article')).
                Form::select('article_form_template', $templates, Option::get('article_template')).
                Html::br().
                Form::submit('article_component_save', __('Save', 'article'), array('class' => 'btn')).        
                Form::close()
            );
        }
	}