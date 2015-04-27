<?php 
    Navigation::add(__('Maps', 'maps'), 'content', 'maps', 10);
    Action::add('admin_pre_render','MapsAdmin::saveSettings');
    
    class MapsAdmin extends Backend {
            
	    public static function main() {
            
            $maps = new Table('maps');
            
            /**
             * Address Insert
             */
	    	if (Request::post('submit_add_address')) {
                if (Security::check(Request::post('csrf'))) {
                    $address = Request::post('address');
                    $phones = (Request::post('phones') != '') ? Request::post('phones') : '' ;
                
                    if($address!='') {
                        $json = Curl::get('http://geocode-maps.yandex.ru/1.x/?format=json&geocode='.urlencode($address));
                        $array = json_decode($json);
                        $coord = $array->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
                        $points = explode(' ',$coord);
        
                        $maps->insert(array('address'=>$address, 'phones'=>$phones, 'lat'=>$points[0], 'lon'=>$points[1]));
                        Request::redirect('index.php?id=maps');
                    }
                } else { die('csrf detected!'); }
            }
            
            /**
             * Address Delete
             */
	    	if (Request::get('delete_id')) {
                if (Security::check(Request::get('token'))) {
                    $maps->delete((int)Request::get('delete_id'));
                    Request::redirect('index.php?id=maps');
                }
            }
            
            $records = $maps->select(null, 'all');
            
            Notification::setNow('address', 'address');
            
            View::factory('maps/views/backend/index')
                ->assign('records', $records)
                ->display();
	    }
        
        /**
         * Save settings
         */
        public static function saveSettings() {

            if(Request::post('maps_submit_settings')) {
                if (Security::check(Request::post('csrf'))) {
                    $width  = (int)Request::post('width');
                    $height = (int)Request::post('height');
                    $zoom   = (int)Request::post('zoom');
                    $zoomc  = (int)Request::post('zoomc');
                    Option::update(array(
                        'map_width' => $width, 
                        'map_height' => $height,
                        'map_zoom' => $zoom, 
                        'map_zoomc' => $zoomc
                    ));
                    exit('<b>'.__('Save settings success!', 'maps').'</b>');
                } else { die('csrf detected!'); }
            }
        }
	}