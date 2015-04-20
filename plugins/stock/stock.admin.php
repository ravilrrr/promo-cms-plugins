<?php 

    // Admin Navigation: add new item
    Navigation::add(__('Stock', 'stock'), 'content', 'stock', 10);

    // Add actions
    Action::add('admin_pre_render','StockAdmin::ajaxSave');
    Action::add('admin_header','StockAdmin::_header', 9);
    Action::add('admin_post_template','StockAdmin::_modal');
    
    /**
     * Stock admin class
     */
    class StockAdmin extends Backend {
    
        public static function _header() {
            if (isset(Plugin::$plugins['markitup'])) {
                echo "<script>
                $(document).ready(function(){
                    mySettings.markupSet.push({
                        name:'Photostock', 
                        beforeInsert: function() { stockModal(); },
                        className:'stock-button' 
                    });
                    
                    $(document).on('click', '.stock-modal-all, .stock-modal-random, .stock-modal-last, .stock-modal-last3, .stock-modal-image', function() { $.markItUp({replaceWith: $(this).attr('rel')});});
                });
                </script>";
                
                echo "<style>.stock-button a {background-image:url(".Site::url()."/plugins/stock/lib/button.png);}</style>";
            }
        }
        
        // Модальное окно
        public static function _modal() {
            View::factory('stock/views/backend/modal')->display(); 
        }
        
        public static function main() {
        
            // Width of small image
            $settings['w'] = 200;
        
            // Height of small image
            $settings['h'] = 150;
        
            // The maximum width of the original image
            $settings['wmax'] = 900;
        
            // The maximum height of the original image
            $settings['hmax'] = 800;
        
            // The way to create previews
            $settings['resize'] = 'crop';
        
            // The sorting method
            $settings['sortby'] = 'date';
            
            // The sort order
            $settings['order'] = 'asc';
            
            // Template
            $settings['template'] = 'default';
            
            // Quality small image
            $settings['quality'] = 100;
            
            // Способы создания миниатюр
            $resize_way = array(
                'download' => __('To download it separately', 'stock'),
                'width'    => __('Respect to the width', 'stock'),
                'height'   => __('Respect to the height', 'stock'),
                'crop'     => __('Similarly, cutting unnecessary', 'stock'),
                'stretch'  => __('Similarly with the expansion', 'stock'), 
            );
            
            // Способы сортировки: по позиции, по дате
            $sort_by = array(
                'position' => __('by position', 'stock'),
                'date' => __('by date', 'stock'), 
            );
            
            // Сортировка по убыванию или по возрастанию
            $sort_order = array(
                'ASC' => __('asc', 'stock'),
                'DESC' => __('desc', 'stock'),
            );
            
            // Типы доп. полей
            $fields_type = array(
                'text' => __('Fields type text', 'stock'),
                'textarea' => __('Fields type textarea', 'stock'),
                'link' => __('Fields type link', 'stock'),
            );
            
            $dir = ROOT . DS . 'public' . DS . 'stock' . DS;
            
            $stock = new Table('stock');
            $stock_img = new Table('stock_img');
            $stock_fields = new Table('stock_fields');
            
            $siteurl = Option::get('siteurl');
            
            /**
             * Templates
             */
            $_templates = File::scan(PLUGINS . DS . 'stock' . DS . 'views' . DS . 'frontend', '.view.php');
            foreach($_templates as $template) {
                $templates[basename($template, '.view.php')] = basename($template, '.view.php');
            }
            
            /**
             * Delete photo full
             */
            if(Request::get('delete_img') and Request::get('album_id') and Request::get('img_id')) {
                if (Security::check(Request::get('token'))) {
                
                    $thumbs = $dir . Request::get('album_id') . DS . 'thumbs' . DS;
                    $original = $dir . Request::get('album_id') . DS . 'original' . DS;
                    
                    if(file_exists($original . Request::get('delete_img'))) {
                        unlink($original . Request::get('delete_img'));
                    }
                    
                     if(file_exists($thumbs . Request::get('delete_img'))) {
                        unlink($thumbs . Request::get('delete_img'));
                    }
                    
                    if (Request::get('onlyimg') == false) {
                        $stock_img->delete(Request::get('img_id'));
                        Request::redirect('index.php?id=stock&album_id='.Request::get('album_id'));
                    } else {
                        Request::redirect('index.php?id=stock&action=img_edit&img_id='.Request::get('img_id'));
                    }
                }
            }
            
            /**
             * Удаление доп. поля
             */
            if(Request::get('field_id_delete') and Request::get('album_id')) {
                if (Security::check(Request::get('token'))) {
                    $stock_fields->delete(Request::get('field_id_delete'));
                    Request::redirect('index.php?id=stock&album_id='.Request::get('album_id'));
                }
            }
            
            /**
             * Удаление альбома
             */
            if(Request::get('delete_album')) {
                if (Security::check(Request::get('token'))) {
                    $album_id = (int)Request::get('delete_album');
                    
                    // Удаление альбома
                    $stock->delete($album_id);
                    
                    // Удаление доп. полей
                    $fields = $stock_fields->select('[album_id='.$album_id.']');
                    if (count($fields) > 0)
                        foreach($fields as $field)
                            $stock_fields->delete($field['id']);
                    
                    // Удаление фотографий
                    $photos = $stock_img->select('[cat_id='.$album_id.']');
                    if (count($photos) > 0)
                        foreach($photos as $photo)
                            $stock_img->delete($photo['id']);
                    
                    $album = $dir . $album_id . DS;
                    $thumbs = $album . 'thumbs' . DS;
                    $original = $album . 'original' . DS;
                
                    if ($objs = glob($thumbs."*"))   foreach($objs as $obj) unlink($obj);
                    if ($objs = glob($original."*")) foreach($objs as $obj) unlink($obj);
                    
                    rmdir($thumbs);
                    rmdir($original);
                    rmdir($album);
                    
                    Request::redirect('index.php?id=stock');
                }
            }
            
            /**
             * Добавление дополнительного поля
             */
            if (Request::post('submit_field_add') && Request::post('fields_name') != '') {
                if (!Security::check(Request::post('csrf'))) {
                    die('csrf detected!');
                }
                
                $name = (string)Request::post('fields_name');
                $slug = (string)Request::post('fields_slug');
                $type = (string)Request::post('fields_type');
                //$pos = (int)Request::post('fields_pos');
                $album_id = (int)Request::post('album_id');
                
                $pos = $stock_fields->lastId() + 1;
                
                // Если поле slug не заполнено, делаем транслит названия
                if (empty($slug)) $slug = $name;
                $slug = Security::safeName($slug, '_', true);
                    
                // Проверяем существует ли такое поле в файле с изображениями stock_img.xml, если нет, создаем
                if ($stock_img->existsField($slug) === false) {
                    $stock_img->addField($slug);
                }
                
                // Записывам новое дополнительное поле
                $stock_fields->insert(array('album_id'=>$album_id, 'name'=>$name, 'slug'=>$slug, 'type'=>$type, 'pos'=>$pos));
                
                Request::redirect('index.php?id=stock&album_id='.$album_id);
            }
            
            /**
             * Добавление альбома
             */
            if (Request::post('submit_album_add')) {
                if (Security::check(Request::post('csrf'))) {
                    $w = (int)Request::post('width_thumb');
                    $h = (int)Request::post('height_thumb');
                    $wmax = (int)Request::post('width_orig');
                    $hmax = (int)Request::post('height_orig');
                    $resize = (string)Request::post('resize_way');
                    $sortby = (string)Request::post('sort_by');
                    $order = (string)Request::post('order');
                    $template = (string)Request::post('template');
                    $name = (string)Request::post('name');
                    $quality = (int)Request::post('quality');
                    
                    if(empty($name)) $name = __('No title', 'stock');
                    
                    $stock->insert(array(
                        'name'=>$name, 
                        'w'=>$w, 
                        'h'=>$h, 
                        'wmax'=>$wmax, 
                        'hmax'=>$hmax, 
                        'resize'=>$resize, 
                        'sortby'=>$sortby, 
                        'order'=>$order,
                        'template'=>$template,
                        'quality'=>$quality,
                    ));
                    
                    $lastid = $stock->lastId();
                    
                    $album_dir = $dir . $lastid . DS;
                    $album_dir_thumbs = $album_dir . 'thumbs' . DS;
                    $album_dir_original = $album_dir . 'original' . DS;
                    
                    if(!is_dir($album_dir)) mkdir($album_dir, 0755);
                    if(!is_dir($album_dir_thumbs)) mkdir($album_dir_thumbs, 0755);
                    if(!is_dir($album_dir_original)) mkdir($album_dir_original, 0755);
                    
                    Request::redirect('index.php?id=stock&album_id='.$lastid);
                } else { die('csrf detected!'); }
            }
            
            /**
             * Редактирование картинки
             */
            if ((Request::post('submit_img_edit') || Request::post('submit_img_edit_and_exit')) && Request::get('img_id')) {
                
                $title = (string)Request::post('title');
                $img_id = (int)Request::get('img_id');
                
                $position = ((int)Request::post('position') > 0) ? (int)Request::post('position') : 0;
                
                if (!Security::check(Request::post('csrf'))) {
                    die('csrf detected!');
                }
                
                $fields_array = (array)Request::post('fields');
                $data = array('pos'=>$position, 'title'=>$title);
                
                $stock_img->update($img_id, array_merge($data, $fields_array));
                
                Notification::set('success', __('Saved', 'stock'));
                
                if(Request::post('submit_img_edit_and_exit') and Request::get('album_id')) {
                    Request::redirect('index.php?id=stock&album_id='.Request::get('album_id'));
                } else {
                    Request::redirect('index.php?id=stock&action=img_edit&img_id='.$img_id.'&album_id='.Request::get('album_id'));
                }
            }
            
            /**
             *  Добавление нового изображения
             */   
            if (Request::post('upload_file')) {
                if (Security::check(Request::post('csrf'))) { 
                    $album_id = (int)Request::post('album_id');
                    
                    if (file_exists($_FILES['file']['tmp_name']) or Request::post('file_link')) {
                        if($_FILES['file']['type'] == 'image/jpeg' ||
                            $_FILES['file']['type'] == 'image/png' ||
                            $_FILES['file']['type'] == 'image/gif' ||
                            Request::post('file_link')) {
                            
                            if (Request::post('file_link')) {
                                Session::set('stock_from', 'internet');
                                $img_name = tempnam(sys_get_temp_dir(), 'stock');
                                copy(Request::post('file_link'), $img_name);
                            } else {
                                Session::set('stock_from', 'computer');
                                $img_name = $_FILES['file']['tmp_name'];
                            }
                            
                            $name = Text::random('alnum', 10).'.jpg';
                            $img  = Image::factory($img_name);
                            $title = Request::post('title');
                            
                            $photo_edit = (Request::post('photo_edit')) ? true : false;
                            
                            $position = ((int)Request::post('position') > 0) ? (int)Request::post('position') : 0;
                            
                            $album = $stock->select('[id='.$album_id.']', null);
                            
                            $wmax    = (int)$album['wmax'];
                            $hmax    = (int)$album['hmax'];
                            $width   = (int)$album['w'];
                            $height  = (int)$album['h'];
                            $quality = (int)$album['quality'];
                            $resize  = $album['resize'];
                            
                            $original = $dir . $album_id . DS . 'original' . DS;
                            $thumbs = $dir . $album_id . DS . 'thumbs' . DS;
                            
                            $ratio = $width/$height;
                            
                            if ($img->width > $wmax or $img->height > $hmax) {
                                if ($img->height > $img->width) {
                                    $img->resize($wmax, $hmax, Image::HEIGHT);
                                } else {
                                    $img->resize($wmax, $hmax, Image::WIDTH);
                                }
                            }
                            $img->save($original . $name, $quality);
                            
                            switch ($resize) {
                                case 'width' :   $img->resize($width, $height, Image::WIDTH);  break;
                                case 'height' :  $img->resize($width, $height, Image::HEIGHT); break;
                                case 'stretch' : $img->resize($width, $height); break;
                                case 'download' :
                                    if (file_exists($_FILES['file_small']['tmp_name']))
                                        $img = Image::factory($_FILES['file_small']['tmp_name']);
                                    else
                                        $img->resize($width, $height);
                                    break;
                                default : 
                                    // crop
                                    if (($img->width/$img->height) > $ratio) {
                                        $img->resize($width, $height, Image::HEIGHT)->crop($width, $height, round(($img->width-$width)/2),0);
                                    } else {
                                        $img->resize($width, $height, Image::WIDTH)->crop($width, $height, 0, 0);
                                    }
                                    break;
                            }
                            
                            if (Request::post('photo_edit')) {
                                // загрузка обновленной фотки
                                $img_id = (int)Request::post('photo_edit');
                                $stock_img->update($img_id, array('name'=>$name));
                            } else {
                                // загрузка новой фотки
                                
                                // Соединяем основные данные с дополнрительными полями
                                $fields_array = (array)Request::post('fields');
                                $data = array('name'=>$name, 'pos'=>$position, 'title'=>$title, 'cat_id'=>$album_id);
        
                                $stock_img->insert(array_merge($data, $fields_array));
                            }
                            
                            $img->save($thumbs . $name, $quality);
                            unlink($img_name);
                        }
                    }
                    Request::redirect('index.php?id=stock&album_id='.$album_id);
                } else { die('csrf detected!'); }
            }
            
            Breadcrumbs::add('index.php?id=stock', __('Stock', 'stock'));
            
            /**
             * Actions
             */
            if (Request::get('action')) {
                switch (Request::get('action')) {
                    case 'addalbum': 
                        
                        Breadcrumbs::add('index.php?id=stock&action=addalbum', __('Add album', 'stock'));
                    
                        if ($stock->count() > 0) $settings = $stock->select('[last()]', null);
                        
                        View::factory('stock/views/backend/album_add')
                            ->assign('resize_way', $resize_way)
                            ->assign('sort_by', $sort_by)
                            ->assign('order', $sort_order)
                            ->assign('templates', $templates)
                            ->assign('settings', $settings)
                            ->display(); 
                        break;
                    case 'img_edit': 
                        if (Request::get('img_id')) {
                            
                            $img_id = (int)Request::get('img_id');
                            
                            $image = $stock_img->select('[id='.$img_id.']', null);
                            
                            $album = $stock->select('[id='.$image['cat_id'].']', null);
                            
                            $fields = $stock_fields->select('[album_id='.$album['id'].']');
                            
                            if(file_exists($dir . $album['id'] . DS . 'original' . DS . $image['name'])) {
                                $photo_exists = true;
                            } else {
                                $photo_exists = false;
                            }
                            
                            
                            Breadcrumbs::add("index.php?id=stock&album_id={$album['id']}", $album['name']);
                            Breadcrumbs::add("index.php?id=stock&action=img_edit&img_id={$img_id}&album_id={$album['id']}", (empty($image['title']) ? __('Edit image', 'stock') : $image['title']));
                            
                            View::factory('stock/views/backend/img_edit')
                                ->assign('img_id', $img_id)
                                ->assign('fields', $fields)
                                ->assign('image', $image)
                                ->assign('album', $album)
                                ->assign('path_orig', $siteurl.'/public/stock/'.$album['id'].'/original/')
                                ->assign('path_thumb', $siteurl.'/public/stock/'.$album['id'].'/thumbs/')
                                ->assign('photo_exists', $photo_exists)
                                ->display();
                        }
                        break;
                }
            } else {
                
                // Вывод альбома и изображений
                if(Request::get('album_id')) {
                    $album = $stock->select('[id='.(int)Request::get('album_id').']', null);
                    $fields = $stock_fields->select('[album_id='.(int)Request::get('album_id').']', 'all', null, null, 'pos');
                    
                    Breadcrumbs::add('index.php?id=stock&album_id='.$album['id'], $album['name']);
                    
                    if($album['sortby'] == 'position') {
                        $images = $stock_img->select('[cat_id='.(int)Request::get('album_id').']');
                        $images = Arr::subvalSort($images, 'pos', $album['order']);
                    } else {
                        $images = $stock_img->select('[cat_id='.(int)Request::get('album_id').']');
                    }
                    
                    $max_position = $stock_img->lastId() + 10;
                    
                    Notification::setNow('upload', 'upload!');
                    
                    if(empty($album['sortby'])) $album['sortby'] = 'date';
                    
                    View::factory('stock/views/backend/album')
                        ->assign('album', $album)
                        ->assign('fields', $fields)
                        ->assign('resize_way', $resize_way)
                        ->assign('fields_type', $fields_type)
                        ->assign('sort_by', $sort_by)
                        ->assign('order', $sort_order)
                        ->assign('files', $images)
                        ->assign('templates', $templates)
                        ->assign('max_position', $max_position)
                        ->assign('path_orig', $siteurl.'/public/stock/'.$album['id'].'/original/')
                        ->assign('path_thumb', $siteurl.'/public/stock/'.$album['id'].'/thumbs/')
                        ->display();
                } else {
                    $records = $stock->select();
                    View::factory('stock/views/backend/index')->assign('records', $records)->display();
                }
            }
	    }
        
        /**
         *  Ajax save
         */ 
        public static function ajaxSave() {
            
            // save album edit
            if (Request::post('stock_submit_album_edit')) {
                if (Security::check(Request::post('csrf'))) {
                    
                    $w = (int)Request::post('width_thumb');
                    $h = (int)Request::post('height_thumb');
                    $wmax = (int)Request::post('width_orig');
                    $hmax = (int)Request::post('height_orig');
                    $resize = (string)Request::post('resize_way');
                    $sortby = (string)Request::post('sort_by');
                    $order = (string)Request::post('order');
                    $name = (string)Request::post('name');
                    $album_id = (int)Request::post('album_id');
                    $template = (string)Request::post('template');
                    $quality = (int)Request::post('quality');
                    
                    if(empty($name)) $name = __('No title', 'stock');
                    
                    $stock = new Table('stock');
                    $stock->update($album_id, array('name'=>$name, 'w'=>$w, 'h'=>$h, 'wmax'=>$wmax, 'hmax'=>$hmax, 'resize'=>$resize, 'sortby'=>$sortby, 'order'=>$order, 'template'=>$template, 'quality'=>$quality));
                    
                    exit('<b>'.__('Saved', 'stock').'</b>');
                } else { die('csrf detected!'); }
            }
            
            // photos resize
            if(Request::post('st_resize') and (int)Request::post('album_id')>0) {
                
                $id = (int)Request::post('album_id');
                
                $dir = ROOT . DS . 'public' . DS . 'stock' . DS;  
                $thumbs   = $dir . $id . DS . 'thumbs' . DS;
                $original = $dir . $id . DS . 'original' . DS;
            
                $files = File::scan($thumbs, 'jpg');
                if (count($files) > 0) {
                    
                    $stock = new Table('stock');
                    $album = $stock->select('[id='.$id.']', null);
                
                    $width   = $album['w'];
                    $height  = $album['h'];
                    $quality = $album['quality'];
                    $resize_way = $album['resize'];
                    $ratio = $width/$height;
                       
                    foreach ($files as $name) {
                        $img = Image::factory($original.$name);
                            
                        switch ($resize_way) {
                            case 'width' : $img->resize($width, $height, Image::WIDTH); break;
                            case 'height' : $img->resize($width, $height, Image::HEIGHT); break;
                            case 'stretch' : $img->resize($width, $height); break;
                            default : 
                                // crop                                    
                                if (($img->width/$img->height) > $ratio) {
                                    $img->resize($width, $height, Image::HEIGHT)->crop($width, $height, round(($img->width-$width)/2),0);
                                } else {
                                    $img->resize($width, $height, Image::WIDTH)->crop($width, $height, 0, 0);
                                }
                                break;
                        }
                        $img->save($thumbs . $name, $quality);
                    }
                }
                exit('<b>'.__('Resize success!', 'stock').'</b>');
            }
        
            // show in modal albums
            if(Request::post('st_modal_albums')) {
                $stock = new Table('stock');
                $records = $stock->select();
                View::factory('stock/views/backend/modal_albums')
                    ->assign('records', $records)
                    ->display(); 
                exit();
            }
            
            // show in modal images from album
            if(Request::post('st_modal_images') and (int)Request::post('album_id')>0) {
                
                $id = (int)Request::post('album_id');
                
                $thumbs = ROOT . DS . 'public' . DS . 'stock' . DS . $id . DS . 'thumbs' . DS;
                $thumbs_link = Site::url().'/public/stock/'.$id.'/thumbs/';
                
                $files = File::scan($thumbs, 'jpg');
                
                echo '<div class="well well-small">';
                if (count($files) > 0) {
                    foreach ($files as $name) {
                        echo '<img src="'.$thumbs_link.$name.'" class="stock-modal-image" onclick="stockModal();" rel=\'{stock album="'.$id.'" img="'.$name.'"}\' width="119" style="display:inline-block; cursor:pointer; margin:2px;"/>';
                    }
                } else {
                    echo __('This album has no photos', 'stock');
                }
                echo '</div>';
                
                exit();
            }
        }
    }