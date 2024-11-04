const mix = require('laravel-mix');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

mix.options({
    processCssUrls: false
 });
 
mix.webpackConfig({
    stats: 'verbose',
    plugins: [
        new MiniCssExtractPlugin(),
    ],
    resolve: {
        extensions: ['.js', '.jsx', '.json', '.css']
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                  loader: 'babel-loader',
                  options: {
                    presets: ['@babel/preset-env'],
                  },
                },
            },
            // {
            //     test: /\.css$/,
            //     use: [
            //         {
            //         loader: MiniCssExtractPlugin.loader, 
            //         },
            //         {
            //             loader: 'css-loader',
            //             options: {
            //               importLoaders: 1
            //             }
            //           },  
            //           {
            //             loader: "postcss-loader",
            //         }
            //     ],
            // },
            {
                test: /\.(png|jpe?g|gif|svg)$/i,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[path][name].[ext]',
                            context: 'public/images',
                            outputPath: 'public/webpack-dist/images',
                        },
                    }
                ],
            },
        ],
    },
});

 mix.css('public/css/dashboard.css', 'webpack-dist/css/dashboard.css');
mix.css('public/css/media-card.css', 'webpack-dist/css/media-card.css');
mix.css('public/css/bootstrap-datetimepicker.min.css', 'webpack-dist/css/bootstrap-datetimepicker.min.css');
mix.css('public/css/bootstrap-toggle.min.css', 'webpack-dist/css/bootstrap-toggle.min.css');
mix.css('public/css/treeview.css', 'webpack-dist/css/treeview.css');
mix.css('public/css/app.css', 'webpack-dist/css/app.css');
mix.css('public/libs/fullcalendar/core/main.css', 'webpack-dist/libs/fullcalendar/core/main.css');
//mix.css('public/flow/style.css', 'webpack-dist/flow/style.css');
mix.css('public/css/instagram.css', 'webpack-dist/css/instagram.css');
mix.css('public/css/richtext.min.css', 'webpack-dist/css/richtext.min.css');
mix.css('public/css/sticky-notes.css', 'webpack-dist/css/sticky-notes.css');
mix.css('public/css/app-custom.css?v=0.1', 'webpack-dist/css/app-custom.css?v=0.1');
mix.css('public/css/custom.css', 'webpack-dist/css/custom.css');
mix.css('public/css/bootstrap.min.css', 'webpack-dist/css/bootstrap.min.css');
mix.css('public/css/global_custom.css', 'webpack-dist/css/global_custom.css');
mix.css('public/css/app-custom.css', 'webpack-dist/css/app-custom.css');
mix.css('public/css/common.css', 'webpack-dist/css/common.css');
mix.css('public/css/mind_map/common.css', 'webpack-dist/css/mind_map/common.css');
mix.css('public/css/mind_map/app.css', 'webpack-dist/css/mind_map/app.css');
mix.css('public/css/mind_map/minicolors/jquery.miniColors.css', 'webpack-dist/css/mind_map/minicolors/jquery.miniColors.css');
mix.js('public/js/jsrender.min.js', 'webpack-dist/js/jsrender.min.js');
mix.js('public/js/jquery.validate.min.js', 'webpack-dist/js/jquery.validate.min.js');
mix.js('public/js/jquery-ui.js', 'webpack-dist/js/jquery-ui.js');
mix.js('public/js/common-helper.js', 'webpack-dist/js/common-helper.js');
mix.js('public/js/ads.js', 'webpack-dist/js/ads.js');
mix.js('public/js/digital-marketing-solution.js', 'webpack-dist/js/digital-marketing-solution.js');
mix.js('public/js/digital-marketing-solution-research.js', 'webpack-dist/js/digital-marketing-solution-research.js');
mix.js('public/js/store-website-brand.js', 'webpack-dist/js/store-website-brand.js');
mix.js('public/js/appointment-request.js', 'webpack-dist/js/appointment-request.js');
mix.js('public/js/bootstrap-datetimepicker.min.js', 'webpack-dist/js/bootstrap-datetimepicker.min.js');
mix.js('public/js/bootstrap-toggle.min.js', 'webpack-dist/js/bootstrap-toggle.min.js');
mix.js('public/js/dialog-build.js', 'webpack-dist/js/dialog-build.js');
mix.js('public/js/fm-tagator.js', 'webpack-dist/js/fm-tagator.js');
mix.js('public/js/jquery.jscroll.min.js', 'webpack-dist/js/jquery.jscroll.min.js');
mix.js('public/js/bootstrap-multiselect.min.js', 'webpack-dist/js/bootstrap-multiselect.min.js');
mix.js('public/js/bootstrap-filestyle.min.js', 'webpack-dist/js/bootstrap-filestyle.min.js');
mix.js('public/js/bug-tracker.js', 'webpack-dist/js/bug-tracker.js');
mix.js('public/js/site-helper.js', 'webpack-dist/js/site-helper.js');
mix.js('public/js/treeview.js', 'webpack-dist/js/treeview.js');
mix.js('public/js/bootstrap-datepicker.min.js', 'webpack-dist/js/bootstrap-datepicker.min.js');
mix.js('public/js/main.js', 'webpack-dist/js/main.js');
mix.js('public/js/jquery-confirm-customized.js', 'webpack-dist/js/jquery-confirm-customized.js');
mix.js('public/js/country-duty-list.js', 'webpack-dist/js/country-duty-list.js');
mix.js('public/js/country-duty.js', 'webpack-dist/js/country-duty.js');
mix.js('public/js/custom_chat_message.js', 'webpack-dist/js/custom_chat_message.js');
mix.js('public/js/zoom-meetings.js', 'webpack-dist/js/zoom-meetings.js');
mix.js('public/js/recorder.js', 'webpack-dist/js/recorder.js');
mix.js('public/js/record-voice-notes.js', 'webpack-dist/js/record-voice-notes.js');
mix.js('public/js/pages/development/development-list.js', 'webpack-dist/js/pages/development/development-list.js');
mix.js('public/js/digital-marketing.js', 'webpack-dist/js/digital-marketing.js');
mix.js('public/js/dnd-list.js', 'webpack-dist/js/dnd-list.js');
mix.js('public/js/simulator.js', 'webpack-dist/js/simulator.js');
mix.js('public/js/clndr/clndr.min.js', 'webpack-dist/js/clndr/clndr.min.js');
mix.js('public/libs/fullcalendar/core/main.js', 'webpack-dist/libs/fullcalendar/core/main.js');
mix.js('public/libs/fullcalendar/daygrid/main.js', 'webpack-dist/libs/fullcalendar/daygrid/main.js');
mix.js('public/libs/fullcalendar/timegrid/main.js', 'webpack-dist/libs/fullcalendar/timegrid/main.js');
mix.js('public/libs/fullcalendar/list/main.js', 'webpack-dist/libs/fullcalendar/list/main.js');
mix.js('public/libs/fullcalendar/interaction/main.js', 'webpack-dist/libs/fullcalendar/interaction/main.js');
mix.js('public/tagify/jQuery.tagify.min.js', 'webpack-dist/tagify/jQuery.tagify.min.js');
mix.js('public/js/rcrop.min.js', 'webpack-dist/js/rcrop.min.js');
mix.js('public/js/hubstaff-activities-notification.js', 'webpack-dist/js/hubstaff-activities-notification.js');
mix.js('public/js/hubstaff-payment.js', 'webpack-dist/js/hubstaff-payment.js');
mix.js('public/js/bootstrap-tagsinput.js', 'webpack-dist/js/bootstrap-tagsinput.js');
mix.js('public/js/store-website.js', 'webpack-dist/js/store-website.js');
mix.js('public/js/instagram.js', 'webpack-dist/js/instagram.js');
mix.js('public/js/bootstrap-notify.js', 'webpack-dist/js/bootstrap-notify.js');
mix.js('public/js/landing-page.js', 'webpack-dist/js/landing-page.js');
mix.js('public/js/app.js', 'webpack-dist/js/app.js');
mix.js('public/js/calls.js', 'webpack-dist/js/calls.js');
mix.js('public/js/custom.js', 'webpack-dist/js/custom.js');
mix.js('public/js/jquery.richtext.js', 'webpack-dist/js/jquery.richtext.js');
mix.js('public/js/jquery.cookie.js', 'webpack-dist/js/jquery.cookie.js');
mix.js('public/js/custom_global_script.js', 'webpack-dist/js/custom_global_script.js');
mix.js('public/js/common-function.js', 'webpack-dist/js/common-function.js');
mix.js('public/js/custom_app.js', 'webpack-dist/js/custom_app.js');
mix.js('public/js/listing-history.js', 'webpack-dist/js/listing-history.js');
mix.js('public/js/load-testing.js', 'webpack-dist/js/load-testing.js');
mix.js('public/js/mock.js', 'webpack-dist/js/mock.js');
mix.js('public/js/jquery.dropdown.min.js', 'webpack-dist/js/jquery.dropdown.min.js');
mix.js('public/js/jquery.dropdown.js', 'webpack-dist/js/jquery.dropdown.js');
mix.js('public/js/magento-product-error.js', 'webpack-dist/js/magento-product-error.js');
mix.js('public/js/manage-modules.js', 'webpack-dist/js/manage-modules.js');
mix.js('public/js/manage-task-category.js', 'webpack-dist/js/manage-task-category.js');
mix.js('public/js/message_queue_history.js', 'webpack-dist/js/message_queue_history.js');
mix.js('public/js/mind_map/script.js', 'webpack-dist/js/mind_map/script.js');
mix.js('public/js/newsletters.js', 'webpack-dist/js/newsletters.js');
mix.js('public/js/order-awb.js', 'webpack-dist/js/order-awb.js');
mix.js('public/js/custom-passwords.js', 'webpack-dist/js/custom-passwords.js');
mix.js('public/js/product-category.js', 'webpack-dist/js/product-category.js');
mix.js('public/js/product-color.js', 'webpack-dist/js/product-color.js');
mix.js('public/js/product-template.js', 'webpack-dist/js/product-template.js');
mix.js('public/js/quick-customer.js', 'webpack-dist/js/quick-customer.js');
mix.js('public/js/return-exchange.js', 'webpack-dist/js/return-exchange.js');
mix.js('public/js/script-documents.js', 'webpack-dist/js/script-documents.js');
mix.js('public/js/template.js', 'webpack-dist/js/template.js');
mix.js('public/js/test-case.js', 'webpack-dist/js/test-case.js');
mix.js('public/js/test-suites.js', 'webpack-dist/js/test-suites.js');
mix.js('public/js/time-doctor-activities-notification.js', 'webpack-dist/js/time-doctor-activities-notification.js');
mix.js('public/vendor/totem/js/app.js', 'webpack-dist/vendor/totem/js/app.js');
mix.js('public/js/bootstrap-weekpicker.js', 'webpack-dist/js/bootstrap-weekpicker.js');
mix.js('public/js/vendor-category.js', 'webpack-dist/js/vendor-category.js');
mix.js('public/js/echo.js', 'webpack-dist/js/echo.js');
mix.js('public/js/email-alert-echo.js', 'webpack-dist/js/email-alert-echo.js');
mix.js('public/js/header-icons-echo.js', 'webpack-dist/js/header-icons-echo.js');

if (mix.inProduction()) {
    mix.version();
}