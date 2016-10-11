<?php namespace wildfire\Product\Controllers;

use Db;
use Backend\Classes\Controller;
use wildfire\Product\Models\Product;
use wildfire\Product\Models\Variant;
use wildfire\Product\Models\Color;
use wildfire\Product\Models\Colorway;
use wildfire\Product\Models\Image;
use wildfire\Product\Models\Size;
use wildfire\Product\Models\Category;
use System\Models\File;
use BackendMenu;
use wildfire\Product\lib\ShopifyClient;
use \Exception;


class Shopify extends Controller
{

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('wildfire.Product', 'shopify-item', 'side-menu-item');
    }

    private function createVariantFromArray($var, $opts, $newprod)
    {

        echo '<br />creating variant';
        $newvar = new Variant;
        $newvar->shopify_product_id = $var['product_id']; 
        $newvar->shopify_variant_id = $var['id'];
        $newvar->price = $var['price'];
        $newvar->sku = $var['sku'];
        $newvar->barcode = $var['barcode'];
        $newvar->shopify_inventory = $var['inventory_quantity'];
        $newvar->weight = $var['weight'];
        $newvar->taxable = $var['taxable'];
        $newvar->sort_order = $var['position'];
        $newvar->product_id = $newprod->id;
        $newvar->save();

        //load variant options
        foreach ($opts AS $idx => $opt)
        {
            $optname = $opt['name'];
            if (strtolower($optname) == 'color' || strtolower($optname) == 'colour' || strtolower($optname) == 'grip color')
            {
                var_dump($var['option'.($idx + 1)]);
                echo "setting for color.. ". $var['option'.($idx + 1)];
                
                $colorsnames = explode('/',$var['option'.($idx + 1)]);
                $found = false;
                $new_ids = [];
                foreach ($colorsnames AS $colorname)
                {
                    if ($foundcolor = Color::where('name',$colorname)->first())
                    {
                        echo "found existing by straight compare (".$foundcolor->name.")";
                        $new_ids[$foundcolor->nest_left] = $foundcolor->id;
                    }
                    else if ($foundcolor = Color::where('name',ucwords(strtolower($colorname)))->first())
                    {
                        echo "found existing by fixing capitalization (".$foundcolor->name.")";
                        $new_ids[$foundcolor->nest_left] = $foundcolor->id;
                    }
                    else
                    {
                        $newcol = new Color();
                        $newcol->hex_code = 'ffff00';
                        $newcol->name = $colorname;
                         echo "made new color--- ".$newcol->name;
                        $newcol->save();
                        $new_ids['999999999'.$colorname] = $newcol->id;
                    }
                }
                ksort($new_ids);
                $new_ids = array_values($new_ids);

                $existings = Colorway::with('colors')->where('product_id', $newprod->id)->get();
                $found = false;
                foreach ($existings AS $existing)
                {
                    $existing_ids = $existing->colors()->lists('id');
                    if ($existing_ids == $new_ids)
                    {
                        $newvar->colorway_id = $existing->id;
                        echo 'using colorway: ' + $existing->colorlist;
                        $found = true;
                    }
                }
                if (!$found)
                {
                    $colorway = new Colorway();
                    $colorway->product_id = $newprod->id;
                    $colorway->save();
                    $colorway->colors()->sync($new_ids);
                    $newvar->colorway_id = $colorway->id;
                    echo 'made new colorway for: ' + $colorway->colorlist;
                }
            } 
            else if (strtolower($optname) == 'size')
            {
                if (!$search = Size::where('name', $var['option'.($idx + 1)])->first())
                {
                    $search = new Size;
                    $search->name = $var['option'.($idx+1)];
                    $search->save();
                    echo 'made new size for: ' + $search->name;
                }
                $newvar->size_id = $search->id;
            }
        }

        $newvar->save();
        return $newvar;
    }

    public function importSports()
    {
        $this->importSite('lizard-sports');
    }

    public function importCycling()
    {
        $this->importSite('lizard-skins');
    }

    public function importSite($shop)
    {
        $shops = [
            'lizard-sports' => ['867349916b5ec06038df58f12c3e7456', '0db86c0b602fb847fff2cbb4e1a7e3f1', 'cf6e319c37c71842ada06993ad98608e'],
            'lizard-skins'  => ['c86994bf2a82f33e1780d7e4e3c82c78', 'fefe5db56c641d5d495ee914af10aefd', 'a6ad8a89279d6c8829a8f84b2ea48793']
        ];
        //$shop = 'lsdev';
        //$shop = 'lizard-skins';
        //$shop = 'lizard-sports';
        $api = new ShopifyClient($shop.'.myshopify.com', $shops[$shop][0], $shops[$shop][1], $shops[$shop][2]);

        $stats = ['inv_updated' => 0, 'desc_updated' => 0, 'vars_added' => 0, 'prods_added' => 0, 'prods_vars_added' => 0, 'skipped' => 0];
        $resp = $api->call('GET','/admin/products.json?limit=250');
        if ($resp)
        {
            foreach ($resp as $product)
            {
                //load options
                $opts = $product['options'];
                $vars = $product['variants'];
                $imgs = $product['images'];
                foreach ($vars as $varidx => $var)
                {
                    echo '<h4>'.$product['title'].' - '. $var['sku'].'</h4>';
                    $foundvar = Variant::where('shopify_variant_id', $var['id'])->first();
                    if (!$foundvar)
                    {
                        //try finding the related product_id
                        $found = Product::where('shopify_id', $var['product_id'])->first();
                        if ($found && ($found->shopify_shop == $shop))
                        {
                            //add as a new variant to an existing product
                            echo '<br />ADDING VARIANT TO  '.$product['title'];
                            $newvar = $this->createVariantFromArray($var, $opts, $found);
                            $saved_vars[$var['id']] = $newvar;
                            $stats['vars_added'] ++;

                            $image = null;

                            if (count($vars) == count($imgs))
                            {
                                $image = $imgs[$varidx];
                            }
                            else if ($var['image_id'])
                            {
                                foreach ($imgs AS $img)
                                {
                                    if ($img['id'] == $var['image_id'])
                                    {
                                        $image = $img;
                                    }
                                }
                            }

                            if ($image)
                            {
                                $ch = curl_init();
                                $source = $image['src']; 
                                echo 'image src: '.$source;
                                curl_setopt($ch, CURLOPT_URL, $source);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                $data = curl_exec ($ch);
                                curl_close ($ch);

                                $name = explode('.',$source);
                                $name = array_pop($name);
                                $name = explode('?', $name);
                                $name = array_shift($name);
                                $destination = "/tmp/".$image['id'].".".$name;
                                $file = fopen($destination, "w+");
                                fputs($file, $data);
                                fclose($file);
                                echo ' dest: '. $destination;

                                $file = new File;
                                $file->data = $destination;
                                $file->save();
                                echo ' fileid: '.$file->id;

                                $image = new Image();
                                $image->product_id = $found->id;
                                $image->colorway_id = $newvar->colorway_id;
                                //$image->size_id = $newvar->size_id;
                                $image->save();
                                $image->image()->add($file);
                                echo ' imageid: '.$image->id;
                            }

                        }
                        else
                        {
                            //create the new product
                            if ($stats['prods_added'] < 10)
                            {
                                echo '<br />CREATING '.$product['title'];
                                //create product;
                                $newprod = new Product;
                                $newprod->shopify_shop = $shop;
                                $newprod->shopify_id = $product['id'];
                                $newprod->import_pending = true;
                                $newprod->name = $product['title'];
                                $newprod->slug = str_slug($product['title']);
                                $newprod->active = ($product['published_scope'] == 'global' || $product['published_scope'] == 'web');
                                $newprod->save();

                                if ($product['vendor'] == 'Lizard Skins Lacrosse')
                                {
                                    $newprod->categories()->attach(Category::where(['name' => 'Lacrosse'])->first()->id);
                                }
                                else if ($product['vendor'] == 'Lizard Skins Baseball')
                                {
                                    $newprod->categories()->attach(Category::where(['name' => 'Baseball'])->first()->id);
                                }
                                else if ($shop == 'lizard-skins')
                                {
                                    $parent = Category::where('name','Cycling')->first();
                                    if ($type_cat = Category::where('name', $product['product_type'])->where('nest_left', '>', $parent->nest_left)->where('nest_right', '<', $parent->nest_right)->first())
                                    {
                                        $newprod->categories()->attach($type_cat->id);
                                    }
                                    else
                                    {
                                        $newprod->categories()->attach(Category::where(['name' => 'Cycling'])->first()->id);
                                    }
                                }
                                else if ($shop == 'lizard-sports')
                                {
                                    $parent = Category::where('name','Sports')->first();
                                    if ($type_cat = Category::where('name', $product['product_type'])->where('nest_left', '>', $parent->nest_left)->where('nest_right', '<', $parent->nest_right)->first())
                                    {
                                        $newprod->categories()->attach($type_cat->id);
                                    }
                                    else
                                    {
                                        $newprod->categories()->attach(Category::where(['name' => 'Sports'])->first()->id);
                                    }
                                }

                                
                                //create variants
                                $vars = $product['variants'];
                                $newvar = $this->createVariantFromArray($var, $opts, $newprod);
                                $stats['prods_vars_added'] ++;

                                //images
                                foreach ($imgs AS $image)
                                {
                                    $ch = curl_init();
                                    $source = $image['src']; 
                                    echo 'image src: '.$source;
                                    curl_setopt($ch, CURLOPT_URL, $source);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                    $data = curl_exec ($ch);
                                    curl_close ($ch);

                                    $name = explode('.',$source);
                                    $name = array_pop($name);
                                    $name = explode('?', $name);
                                    $name = array_shift($name);
                                    $destination = "/tmp/".$image['id'].".".$name;
                                    $file = fopen($destination, "w+");
                                    fputs($file, $data);
                                    fclose($file);
                                    echo ' dest: '. $destination;

                                    $file = new File;
                                    $file->data = $destination;
                                    $file->save();
                                    echo ' fileid: '.$file->id;

                                    $image = new Image();
                                    $image->product_id = $newprod->id;
                                    $image->save();
                                    $image->image()->add($file);
                                    echo ' imageid: '.$image->id;
                                }
                                $stats['prods_added'] ++;
                            }
                            else
                            {
                                $stats['skipped'] ++;
                                echo '<br />already added 10 products.';
                                continue;
                            }

                        }

                    }
                    else
                    {
                        //correct product with single colorway but not assigned to any
                        $parprod = Product::with('colorways')->find($foundvar->product_id);
                        if (!$foundvar->colorway_id && $parprod && $parprod->colorways()->count() == 1)
                        {
                            $colorway = $parprod->colorways()->first();
                            echo "setting single product colorway to sole value of ".$colorway->colorlist;
                            $foundvar->colorway_id = $colorway->id;
                            $foundvar->save();
                        }
                        //assign images to colorways blindly
                        else if ($parprod->colorways()->count() == $parprod->images()->count() && ($novar_images = Image::where('product_id',$parprod->id)->whereNull('colorway_id')->get()))
                        {
                            $colorways = $parprod->colorways()->get();
                            foreach ($novar_images AS $i => $img)
                            {
                                echo " <br />assigning image to colorway ".$colorways[$i]->colorlist;
                                $img->colorway_id = $colorways[$i]->id;
                                $img->save();
                            }
                        }
                        //correct problem with missing colorway
                        else if (!$foundvar->colorway_id)
                        {
                            foreach ($opts AS $idx => $opt)
                            {
                                $optname = $opt['name'];
                                if (strtolower($optname) == 'color' || strtolower($optname) == 'colour' || strtolower($optname) == "grip color")
                                {
                                    echo " fixing for color.. ". $var['option'.($idx + 1)];
                                    
                                    $colorsnames = explode('/',$var['option'.($idx + 1)]);
                                    $found = false;
                                    $new_ids = [];
                                    foreach ($colorsnames AS $colorname)
                                    {
                                        if ($foundcolor = Color::where('name',$colorname)->first())
                                        {
                                            echo " found existing by straight compare (".$foundcolor->name.")";
                                            
                                            $new_ids[$foundcolor->nest_left] = $foundcolor->id;
                                        }
                                        else if ($foundcolor = Color::where('name',ucwords(strtolower($colorname)))->first())
                                        {
                                            echo " found existing by fixing capitalization (".$foundcolor->name.")";
                                            $new_ids[$foundcolor->nest_left] = $foundcolor->id;
                                        }
                                        else
                                        {
                                            $newcol = new Color();
                                            $newcol->hex_code = 'ffff00';
                                            $newcol->name = $colorname;
                                                echo "made new color--- ".$newcol->name;
                                            $newcol->save();
                                            $new_ids['999999999'.$colorname] = $newcol->id;
                                        }
                                    }
                                    ksort($new_ids);
                                    $new_ids = array_values($new_ids);
                                    $existings = Colorway::with('colors')->where('product_id',$parprod->id)->get();
                                    $found = false;
                                    foreach ($existings AS $existing)
                                    {
                                        $existing_ids = $existing->colors()->lists('id');
                                        if ($existing_ids == $new_ids)
                                        {
                                            $foundvar->colorway_id = $existing->id;
                                            $foundvar->save();
                                            echo 'using existing product colorway: ' + $existing->colorlist;
                                            $found = true;
                                        }
                                    }
                                    if (!$found)
                                    {
                                        $colorway = new Colorway();
                                        $colorway->product_id = $parprod->id;
                                        $colorway->save();
                                        $colorway->colors()->sync($new_ids);
                                        $foundvar->colorway_id = $colorway->id;
                                        $foundvar->save();
                                        echo 'made new colorway for: ' + $colorway->colorlist;
                                    }
                                } 
                            }
                            

                        }
                        if ($foundvar->shopify_variant_id != $var['id'])
                        {
                            echo '<br />UPDATING variant ID from '.$foundvar->shopify_variant_id.' to '.$var['id'];
                            $foundvar->shopify_variant_id = $var['id'];
                            $foundvar->save();
                        }
                        if ($foundvar->shopify_product_id != $var['product_id'])
                        {
                            echo '<br />UPDATING variant product id from '.$foundvar->shopify_product_id.' to '.$var['product_id'];
                            $foundvar->shopify_product_id = $var['product_id'];
                            $foundvar->save();
                        }

                        if ($foundvar->shopify_inventory != $var['inventory_quantity'])
                        {
                                echo '<br />UPDATING inventory, from '.$foundvar->shopify_inventory.' to '.$var['inventory_quantity'];
                                $foundvar->shopify_inventory = $var['inventory_quantity'];
                                $foundvar->save();
                                $stats['inv_updated'] ++;
                        }
                        if (empty(trim($parprod->description)) && $product['body_html'])
                        {
                            echo '<br />ADDING description';
                            $parprod->description = $product['body_html'];
                            $parprod->save();
                            $stats['desc_updated'] ++;
                        }
                            /*
                            if ($parprod->shopify_id != $product['id'])
                            {
                                echo '<br />UPDATING product product id from '.$parprod->shopify_id.' to '.$product['id'].' ('.$foundvar->shopify_product_id.' / v: '.$foundvar->shopify_variant_id.')';
                                $parprod->shopify_id = $product['id'];
                                $parprod->save();
                            }
                             */

                    }

                }
            }
            var_dump($stats);
            die();
        }
    } 

    public function bestsellers()
    {
        set_time_limit(0);
        //$this->bestsellers_site('lizard-sports');
    //    $this->bestsellers_site('lizard-skins');
    }

    public function bestsellers_site($shop)
    {
        $shops = [
            'lizard-sports' => ['867349916b5ec06038df58f12c3e7456', '0db86c0b602fb847fff2cbb4e1a7e3f1', 'cf6e319c37c71842ada06993ad98608e'],
            'lizard-skins'  => ['c86994bf2a82f33e1780d7e4e3c82c78', 'fefe5db56c641d5d495ee914af10aefd', 'a6ad8a89279d6c8829a8f84b2ea48793']
        ];
        $api = new ShopifyClient($shop.'.myshopify.com', $shops[$shop][0], $shops[$shop][1], $shops[$shop][2]);

        $iteration = 1;

        $page = 1;
        $count = 0;
        $amt = $api->call('GET','/admin/orders/count.json?status=any&created_at_min=2015-08-01T16:15:47-04:00');
        $tally = array();

        while ($resp = $api->call('GET','/admin/orders.json?status=any&page='.$page.'&limit=250&created_at_min=2015-08-01T16:15:47-04:00&fields=line_items')) {
            if (count($resp) == 0 || $page > 25){
                break;
            }
            foreach ($resp AS $order){
                foreach ($order['line_items'] AS $item)
                {
                    if (!isset($tally[$item['variant_id']])){
                        $tally[$item['variant_id']] = array('count' => 1, 'name' => $item['title']);
                    } else {
                        $tally[$item['variant_id']]['count'] ++;
                    }
                }
            }    

            $count += count($resp);
            $page ++;
        }
        foreach ($tally AS $sid => $tosave)
        {
            if ($sid)
            {
                Db::table('wildfire_product_variant')->where('shopify_variant_id', $sid)->increment('cart_adds', $tosave['count']);
            }
        }
        var_dump($tally);
        var_dump('done! '.$count);
        die();

    }

    public function resetBadColorways()
    {
        $vars = Variant::whereNotNull('colorway_id')->with('product')->with('colorway')->get();
        foreach ($vars as $var)
        {
            $varcol = $var->colorway()->first();
            if ($var->colorway()->first()->product_id != $var->product_id)
            {
                echo "<br />removing colorway ".$var->colorway_id." from ".$var->product()->first()->name." - ".$var->sku;
                $var->colorway_id = null;
                $var->temp_colorway = null;
                echo ' - set to '. $var->colorway_id;
                $var->save();
                echo ' - after save: '. $var->colorway_id;
            }
        }
        die();

    }
}
