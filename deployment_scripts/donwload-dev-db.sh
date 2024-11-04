#!/bin/bash

SCRIPT_NAME=`basename $0`

DOWNLOAD_PATH="/var/www/erp.theluxuryunlimited.com/storage/app/download_db"

MY_CREDS=/opt/etc/mysql-creds.conf
source $MY_CREDS
DATES=`date +%s`
args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                -t|--type)
                type="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -s|--server)
                ip="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -n|--instance)
                instance="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -d|--database)
                database="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -h|--help)
                HELP
                exit 1
                ;;
                *)
                	echo "Please verify options"
                ;;
        esac
done

echo "type=$type, ip=$ip, instance=$instance, database=$database" > /var/www/erp.theluxuryunlimited.com/storage/app/download_db/logsfile

if [ "$instance" -eq "1" ]
then
	ER="instance 1" 
else 
	db_instance="_2"
fi

for port in $possible_ssh_port
do
	telnet_output=`echo quit | telnet $ip $port 2>/dev/null | grep Connected`
	if [ ! -z "$telnet_output" ]
	then
		SSH_PORT=$port 
	fi
done

if [ "$type" == "db" ]
then
	ssh -n -p $SSH_PORT -i $SSH_KEY root@$ip "mysqldump --ignore-table=$database.gift_message --ignore-table=$database.quote --ignore-table=$database.quote_address --ignore-table=$database.quote_address_item --ignore-table=$database.quote_id_mask --ignore-table=$database.quote_item --ignore-table=$database.quote_item_option --ignore-table=$database.quote_payment --ignore-table=$database.quote_shipping_rate --ignore-table=$database.reporting_orders --ignore-table=$database.sales_bestsellers_aggregated_daily --ignore-table=$database.sales_bestsellers_aggregated_monthly --ignore-table=$database.sales_bestsellers_aggregated_yearly --ignore-table=$database.sales_creditmemo --ignore-table=$database.sales_creditmemo_comment --ignore-table=$database.sales_creditmemo_grid --ignore-table=$database.sales_creditmemo_item --ignore-table=$database.sales_invoice --ignore-table=$database.sales_invoiced_aggregated --ignore-table=$database.sales_invoiced_aggregated_order --ignore-table=$database.sales_invoice_comment --ignore-table=$database.sales_invoice_grid --ignore-table=$database.sales_invoice_item --ignore-table=$database.sales_order --ignore-table=$database.sales_order_address --ignore-table=$database.sales_order_aggregated_created --ignore-table=$database.sales_order_aggregated_updated --ignore-table=$database.sales_order_grid --ignore-table=$database.sales_order_item --ignore-table=$database.sales_order_payment --ignore-table=$database.sales_order_status_history --ignore-table=$database.sales_order_tax --ignore-table=$database.sales_order_tax_item --ignore-table=$database.sales_payment_transaction --ignore-table=$database.sales_refunded_aggregated --ignore-table=$database.sales_refunded_aggregated_order --ignore-table=$database.sales_shipment --ignore-table=$database.sales_shipment_comment --ignore-table=$database.sales_shipment_grid --ignore-table=$database.sales_shipment_item --ignore-table=$database.sales_shipment_track --ignore-table=$database.sales_shipping_aggregated --ignore-table=$database.sales_shipping_aggregated_order --ignore-table=$database.tax_order_aggregated_created --ignore-table=$database.tax_order_aggregated_updated --ignore-table=$database.customer_address_entity --ignore-table=$database.customer_address_entity_datetime --ignore-table=$database.customer_address_entity_decimal --ignore-table=$database.customer_address_entity_int --ignore-table=$database.customer_address_entity_text --ignore-table=$database.customer_address_entity_varchar --ignore-table=$database.customer_entity --ignore-table=$database.customer_entity_datetime --ignore-table=$database.customer_entity_decimal --ignore-table=$database.customer_entity_int --ignore-table=$database.customer_entity_text --ignore-table=$database.customer_entity_varchar --ignore-table=$database.customer_grid_flat --ignore-table=$database.customer_log --ignore-table=$database.customer_visitor --ignore-table=$database.persistent_session --ignore-table=$database.wishlist --ignore-table=$database.wishlist_item --ignore-table=$database.wishlist_item_option --ignore-table=$database.review --ignore-table=$database.review_detail --ignore-table=$database.review_entity_summary --ignore-table=$database.review_store --ignore-table=$database.cataloginventory_stock_item --ignore-table=$database.cataloginventory_stock_status --ignore-table=$database.cataloginventory_stock_status_idx --ignore-table=$database.cataloginventory_stock_status_tmp --ignore-table=$database.catalog_category_product --ignore-table=$database.catalog_category_product_index --ignore-table=$database.catalog_category_product_index_tmp --ignore-table=$database.catalog_compare_item --ignore-table=$database.catalog_product_bundle_option --ignore-table=$database.catalog_product_bundle_option_value --ignore-table=$database.catalog_product_bundle_price_index --ignore-table=$database.catalog_product_bundle_selection --ignore-table=$database.catalog_product_bundle_selection_price --ignore-table=$database.catalog_product_bundle_stock_index --ignore-table=$database.catalog_product_entity --ignore-table=$database.catalog_product_entity_datetime --ignore-table=$database.catalog_product_entity_decimal --ignore-table=$database.catalog_product_entity_gallery --ignore-table=$database.catalog_product_entity_int --ignore-table=$database.catalog_product_entity_media_gallery --ignore-table=$database.catalog_product_entity_media_gallery_value --ignore-table=$database.catalog_product_entity_media_gallery_value_to_entity --ignore-table=$database.catalog_product_entity_media_gallery_value_video --ignore-table=$database.catalog_product_entity_text --ignore-table=$database.catalog_product_entity_tier_price --ignore-table=$database.catalog_product_entity_varchar --ignore-table=$database.catalog_product_index_eav --ignore-table=$database.catalog_product_index_eav_decimal --ignore-table=$database.catalog_product_index_eav_decimal_idx --ignore-table=$database.catalog_product_index_eav_decimal_tmp --ignore-table=$database.catalog_product_index_eav_idx --ignore-table=$database.catalog_product_index_eav_tmp --ignore-table=$database.catalog_product_index_price --ignore-table=$database.catalog_product_index_price_bundle_idx --ignore-table=$database.catalog_product_index_price_bundle_opt_idx --ignore-table=$database.catalog_product_index_price_bundle_opt_tmp --ignore-table=$database.catalog_product_index_price_bundle_sel_idx --ignore-table=$database.catalog_product_index_price_bundle_sel_tmp --ignore-table=$database.catalog_product_index_price_bundle_tmp --ignore-table=$database.catalog_product_index_price_cfg_opt_agr_idx --ignore-table=$database.catalog_product_index_price_cfg_opt_agr_tmp --ignore-table=$database.catalog_product_index_price_cfg_opt_idx --ignore-table=$database.catalog_product_index_price_cfg_opt_tmp --ignore-table=$database.catalog_product_index_price_downlod_idx --ignore-table=$database.catalog_product_index_price_downlod_tmp --ignore-table=$database.catalog_product_index_price_final_idx --ignore-table=$database.catalog_product_index_price_final_tmp --ignore-table=$database.catalog_product_index_price_idx --ignore-table=$database.catalog_product_index_price_opt_agr_idx --ignore-table=$database.catalog_product_index_price_opt_agr_tmp --ignore-table=$database.catalog_product_index_price_opt_idx --ignore-table=$database.catalog_product_index_price_opt_tmp --ignore-table=$database.catalog_product_index_price_tmp --ignore-table=$database.catalog_product_index_tier_price --ignore-table=$database.catalog_product_index_website --ignore-table=$database.catalog_product_link --ignore-table=$database.catalog_product_link_attribute_decimal --ignore-table=$database.catalog_product_link_attribute_int --ignore-table=$database.catalog_product_link_attribute_varchar --ignore-table=$database.catalog_product_option --ignore-table=$database.catalog_product_option_price --ignore-table=$database.catalog_product_option_title --ignore-table=$database.catalog_product_option_type_price --ignore-table=$database.catalog_product_option_type_title --ignore-table=$database.catalog_product_option_type_value --ignore-table=$database.catalog_product_relation --ignore-table=$database.catalog_product_super_attribute --ignore-table=$database.catalog_product_super_attribute_label --ignore-table=$database.catalog_product_super_link --ignore-table=$database.catalog_product_website --ignore-table=$database.catalog_url_rewrite_product_category --ignore-table=$database.downloadable_link --ignore-table=$database.downloadable_link_price --ignore-table=$database.downloadable_link_purchased --ignore-table=$database.downloadable_link_purchased_item --ignore-table=$database.downloadable_link_title --ignore-table=$database.downloadable_sample --ignore-table=$database.downloadable_sample_title --ignore-table=$database.product_alert_price --ignore-table=$database.product_alert_stock --ignore-table=$database.report_compared_product_index --ignore-table=$database.report_viewed_product_aggregated_daily --ignore-table=$database.report_viewed_product_aggregated_monthly --ignore-table=$database.report_viewed_product_aggregated_yearly --ignore-table=$database.report_viewed_product_index $database" >  /tmp/$database$DATES.sql  | tee -a ${SCRIPT_NAME}.log
	ssh -n -p $SSH_PORT -i $SSH_KEY root@$ip "mysqldump --no-data $database  gift_message quote quote_address quote_address_item quote_id_mask quote_item quote_item_option quote_payment quote_shipping_rate reporting_orders sales_bestsellers_aggregated_daily sales_bestsellers_aggregated_monthly sales_bestsellers_aggregated_yearly sales_creditmemo sales_creditmemo_comment sales_creditmemo_grid sales_creditmemo_item sales_invoice sales_invoiced_aggregated sales_invoiced_aggregated_order sales_invoice_comment sales_invoice_grid sales_invoice_item sales_order sales_order_address sales_order_aggregated_created sales_order_aggregated_updated sales_order_grid sales_order_item sales_order_payment sales_order_status_history sales_order_tax sales_order_tax_item sales_payment_transaction sales_refunded_aggregated sales_refunded_aggregated_order sales_shipment sales_shipment_comment sales_shipment_grid sales_shipment_item sales_shipment_track sales_shipping_aggregated sales_shipping_aggregated_order tax_order_aggregated_created tax_order_aggregated_updated customer_address_entity customer_address_entity_datetime customer_address_entity_decimal customer_address_entity_int customer_address_entity_text customer_address_entity_varchar customer_entity customer_entity_datetime customer_entity_decimal customer_entity_int customer_entity_text customer_entity_varchar customer_grid_flat customer_log customer_visitor persistent_session wishlist wishlist_item wishlist_item_option review review_detail review_entity_summary review_store cataloginventory_stock_item cataloginventory_stock_status cataloginventory_stock_status_idx cataloginventory_stock_status_tmp catalog_category_product catalog_category_product_index catalog_category_product_index_tmp catalog_compare_item catalog_product_bundle_option catalog_product_bundle_option_value catalog_product_bundle_price_index catalog_product_bundle_selection catalog_product_bundle_selection_price catalog_product_bundle_stock_index catalog_product_entity catalog_product_entity_datetime catalog_product_entity_decimal catalog_product_entity_gallery catalog_product_entity_int catalog_product_entity_media_gallery catalog_product_entity_media_gallery_value catalog_product_entity_media_gallery_value_to_entity catalog_product_entity_media_gallery_value_video catalog_product_entity_text catalog_product_entity_tier_price catalog_product_entity_varchar catalog_product_index_eav catalog_product_index_eav_decimal catalog_product_index_eav_decimal_idx catalog_product_index_eav_decimal_tmp catalog_product_index_eav_idx catalog_product_index_eav_tmp catalog_product_index_price catalog_product_index_price_bundle_idx catalog_product_index_price_bundle_opt_idx catalog_product_index_price_bundle_opt_tmp catalog_product_index_price_bundle_sel_idx catalog_product_index_price_bundle_sel_tmp catalog_product_index_price_bundle_tmp catalog_product_index_price_cfg_opt_agr_idx catalog_product_index_price_cfg_opt_agr_tmp catalog_product_index_price_cfg_opt_idx catalog_product_index_price_cfg_opt_tmp catalog_product_index_price_downlod_idx catalog_product_index_price_downlod_tmp catalog_product_index_price_final_idx catalog_product_index_price_final_tmp catalog_product_index_price_idx catalog_product_index_price_opt_agr_idx catalog_product_index_price_opt_agr_tmp catalog_product_index_price_opt_idx catalog_product_index_price_opt_tmp catalog_product_index_price_tmp catalog_product_index_tier_price catalog_product_index_website catalog_product_link catalog_product_link_attribute_decimal catalog_product_link_attribute_int catalog_product_link_attribute_varchar catalog_product_option catalog_product_option_price catalog_product_option_title catalog_product_option_type_price catalog_product_option_type_title catalog_product_option_type_value catalog_product_relation catalog_product_super_attribute catalog_product_super_attribute_label catalog_product_super_link catalog_product_website catalog_url_rewrite_product_category downloadable_link downloadable_link_price downloadable_link_purchased downloadable_link_purchased_item downloadable_link_title downloadable_sample downloadable_sample_title product_alert_price product_alert_stock report_compared_product_index report_viewed_product_aggregated_daily report_viewed_product_aggregated_monthly report_viewed_product_aggregated_yearly report_viewed_product_index >>  /tmp/$database$DATES.sql"  | tee -a ${SCRIPT_NAME}.log

	if [ "$?" -eq "0" ]
	then
	    MESSAGE=`scp -P $SSH_PORT -i $SSH_KEY root@$ip:/tmp/$database$DATES.sql $DOWNLOAD_PATH/$database.sql` | tee -a ${SCRIPT_NAME}.log
		  echo "{\"status\":\"true\",\"message\":\"Dump created in $DOWNLOAD_PATH/$database.sql\",\"url\":\"$DOWNLOAD_PATH/$database.sql\"}"
	else
  		echo "{\"status\":\"FAILED\",\"message\":\"$MESSAGE\",\"url\":\"$DOWNLOAD_PATH/$database.sql\"}" 
	fi
else
	MESSAGE=`scp -P $SSH_PORT -i $SSH_KEY root@$ip:/home/prod-1-1/current/app/etc/env.php $DOWNLOAD_PATH/env.php`  | tee -a ${SCRIPT_NAME}.log
	if [ "$?" -eq "0" ]
	then
	  	echo "{\"status\":\"true\",\"message\":\"env created in $DOWNLOAD_PATH/env.php\",\"url\":\"$DOWNLOAD_PATH/env.php\"}"
	else
	  	echo "{\"status\":\"FAILED\",\"message\":\"$MESSAGE\",\"url\":\"NA\"}"
	fi
fi

#Call monitor_bash_scripts

sh $SCRIPTS_PATH/monitor_bash_scripts.sh ${SCRIPT_NAME} ${STATUS} ${SCRIPT_NAME}.log
