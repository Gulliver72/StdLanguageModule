<?php
namespace Gulliver72\ModifiedStdLanguageModule\Classes;

class StdLanguageModule
{
    protected $code;
    protected $data;
    protected $lngID_from;
    protected $languagesId;

    public function init(array $data, int $lngID_from)
    {

        $this->languagesId = '';
        $this->code = $data['code'];
        $this->lngID_from = $lngID_from;
        $this->data = $data;

        if ($this->checkLanguageIsSet($this->code) === false)
        {
            $this->setLanguage();
        }
    }

    protected function checkLanguageIsSet(): bool
    {
        $query = xtc_db_query("SELECT languages_id FROM " . TABLE_LANGUAGES . " WHERE code = '$this->code'");
        $res = xtc_db_num_rows($query);

        return ($res > 0);
    }

    protected function setLanguage()
    {
        $this->createLanguage();

        if ($this->languagesId != '')
        {
            $this->expandCategoriesDesc();
            $this->expandProductDesc();
            $this->expandProductOptions();
            $this->expandProductOptionsValues();
            $this->expandManufacturersInfo();
            $this->expandOrdersStatus();
            $this->expandProductsXsellGroups();
            $this->expandContentManager();
            $this->expandProductsContent();
        }
    }

    protected function createLanguage()
    {
        $name = $this->data['name'] ?? '';
        $code = $this->code;
        $image = $this->data['image'] ?? '';
        $directory = $this->data['directory'] ?? '';
        $sort_order = $this->data['sort_order'] ?? '';
        $language_charset = $this->data['language_charset'] ?? '';
        $status = 0;
        $status_admin = 0;

        xtc_db_query("INSERT INTO " . TABLE_LANGUAGES . " (name, code, image, directory, sort_order, language_charset, status, status_admin) VALUES ('$name', '$code', '$image', '$directory', '$sort_order', '$language_charset', '$status', '$status_admin')");

        $this->languagesId = xtc_db_insert_id();
    }

    protected function expandCategoriesDesc()
    {
        xtc_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where language_id = '" . $this->languagesId . "'");

        $add_meta = 'cd.categories_meta_title, cd.categories_meta_description, cd.categories_meta_keywords,';
        $categories_query = xtc_db_query("select " . $add_meta . " c.categories_id, cd.categories_name, cd.categories_description from " . TABLE_CATEGORIES . " c left join " . TABLE_CATEGORIES_DESCRIPTION . " cd on c.categories_id = cd.categories_id where cd.language_id = '" . $this->lngID_from . "'");

        while ($categories = xtc_db_fetch_array($categories_query))
        {
            $sql_data_array = $categories;
            $sql_data_array['language_id'] = $this->languagesId;

            xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
        }
    }

    protected function expandProductDesc()
    {
        xtc_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where language_id = '" . $this->languagesId . "'");

        $add_meta = 'pd.products_meta_title, pd.products_meta_description, pd.products_meta_keywords,';
        $products_query = xtc_db_query("select " . $add_meta . " p.products_id, pd.products_name, pd.products_description, pd.products_short_description, pd.products_order_description, pd.products_keywords, pd.products_url from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id where pd.language_id = '" . $this->lngID_from . "'");

        while ($products = xtc_db_fetch_array($products_query))
        {
            $sql_data_array = $products;
            $sql_data_array['language_id'] = $this->languagesId;

            xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
        }
    }

    protected function expandProductOptions()
    {
        xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $this->languagesId . "'");

        $products_options_query = xtc_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $this->lngID_from . "'");

        while ($products_options = xtc_db_fetch_array($products_options_query))
        {
            $sql_data_array = $products_options;
            $sql_data_array['language_id'] = $this->languagesId;

            xtc_db_perform(TABLE_PRODUCTS_OPTIONS, $sql_data_array);
        }
    }

    protected function expandProductOptionsValues()
    {
        xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where language_id = '" . $this->languagesId . "'");

        $products_options_values_query = xtc_db_query("select products_options_values_id, products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where language_id = '" . $this->lngID_from . "'");

        while ($products_options_values = xtc_db_fetch_array($products_options_values_query))
        {
            $sql_data_array = $products_options_values;
            $sql_data_array['language_id'] = $this->languagesId;

            xtc_db_perform(TABLE_PRODUCTS_OPTIONS_VALUES, $sql_data_array);
        }
    }

    protected function expandManufacturersInfo()
    {
        xtc_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where language_id = '" . $this->languagesId . "'");

        $add_meta = 'mi.manufacturers_meta_title, mi.manufacturers_meta_description, mi.manufacturers_meta_keywords,';
        $manufacturers_query = xtc_db_query("select " . $add_meta . " m.manufacturers_id, mi.manufacturers_url, mi.manufacturers_description from " . TABLE_MANUFACTURERS . " m left join " . TABLE_MANUFACTURERS_INFO . " mi on m.manufacturers_id = mi.manufacturers_id where mi.languages_id = '" . $this->lngID_from . "'");

        while ($manufacturers = xtc_db_fetch_array($manufacturers_query))
        {
            $sql_data_array = $manufacturers;
            $sql_data_array['languages_id'] = $this->languagesId;

            xtc_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);
        }
    }

    protected function expandOrdersStatus()
    {
        xtc_db_query("delete from " . TABLE_ORDERS_STATUS . " where language_id = '" . $this->languagesId . "'");

        $orders_status_query = xtc_db_query("select * from " . TABLE_ORDERS_STATUS . " where language_id = '" . $this->lngID_from . "'");

        while ($orders_status = xtc_db_fetch_array($orders_status_query))
        {
            $sql_data_array = $orders_status;
            $sql_data_array['language_id'] = $this->languagesId;

            xtc_db_perform(TABLE_ORDERS_STATUS, $sql_data_array);
        }
    }

    protected function expandShippingStatus()
    {
        xtc_db_query("delete from " . TABLE_SHIPPING_STATUS . " where language_id = '" . $this->languagesId . "'");

        $shipping_status_query = xtc_db_query("select * from " . TABLE_SHIPPING_STATUS . " where language_id = '" . $this->lngID_from . "'");

        while ($shipping_status = xtc_db_fetch_array($shipping_status_query))
        {
            $sql_data_array = $shipping_status;
            $sql_data_array['language_id'] = $this->languagesId;

            xtc_db_perform(TABLE_SHIPPING_STATUS, $sql_data_array);
        }
    }

    protected function expandProductsXsellGroups()
    {
        xtc_db_query("delete from " . TABLE_PRODUCTS_XSELL_GROUPS . " where language_id = '" . $this->languagesId . "'");

        $xsell_grp_query = xtc_db_query("select products_xsell_grp_name_id,xsell_sort_order, groupname from " . TABLE_PRODUCTS_XSELL_GROUPS . " where language_id = '" . $this->lngID_from . "'");

        while ($xsell_grp = xtc_db_fetch_array($xsell_grp_query))
        {
            $sql_data_array = $xsell_grp;
            $sql_data_array['language_id'] = $this->languagesId;

            xtc_db_perform(TABLE_PRODUCTS_XSELL_GROUPS, $sql_data_array);
        }
    }

    protected function expandContentManager()
    {
        xtc_db_query("delete from " . TABLE_CONTENT_MANAGER . " where languages_id = '" . $this->languagesId . "'");

        $content_manager_query = xtc_db_query("select * from " . TABLE_CONTENT_MANAGER . " where languages_id = '" . $this->lngID_from . "'");

        while ($content_manager = xtc_db_fetch_array($content_manager_query))
        {
            $sql_data_array = $content_manager;
            $sql_data_array['languages_id'] = $this->languagesId;
            unset($sql_data_array['content_id']);

            xtc_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array);
        }
    }

    protected function expandProductsContent()
    {
        xtc_db_query("delete from " . TABLE_PRODUCTS_CONTENT . " where languages_id = '" . $this->languagesId . "'");

        $products_content_query = xtc_db_query("select * from " . TABLE_PRODUCTS_CONTENT . " where languages_id = '" . $this->lngID_from . "'");

        while ($products_content = xtc_db_fetch_array($products_content_query))
        {
            $sql_data_array = $products_content;
            $sql_data_array['languages_id'] = $this->languagesId;
            unset($sql_data_array['content_id']);

            xtc_db_perform(TABLE_PRODUCTS_CONTENT, $sql_data_array);
        }
    }
}
