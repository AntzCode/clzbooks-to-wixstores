<?php

// phpinfo();

// note: if you want to protect this script with a password then set it here
$password = 'abc123';

if (!empty($_POST) && !empty($_FILES)) {
    if (!empty($password) && $password !== $_POST['password']) {
        exit('Invalid password');
    }
    ob_start();

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    ini_set('memory_limit', '128M');

    set_time_limit(60);

    // $sourcefile = realpath(__DIR__).DIRECTORY_SEPARATOR.'export_books_3.csv';
    $sourcefile = $_FILES['file']['tmp_name'];
    $destfile = realpath(__DIR__).DIRECTORY_SEPARATOR.'dest.csv';

    @unlink($destfile);

    $uniqueIsbns = [];

    $sourceColumnHeaderNames = [
        'AUTHOR' => 'Author',
        'TITLE' => 'Title',
        'ISBN' => 'ISBN',
        'FORMAT' => 'Format',
        'PAGES' => 'Pages',
        'PUBLISHER' => 'Publisher',
        'PUBLICATION_DATE' => 'Publication Date',
        'GENRE' => 'Genre',
        'ADDED_DATE' => 'Added Date',
        'COVER_PRICE' => 'Cover Price',
        'QUANTITY' => 'Quantity',
        'CONDITION' => 'Condition',
        'SUBJECT' => 'Subject',
        'EDITION' => 'Edition',
        'PLOT' => 'Plot',
        'CURRENT_VALUE' => 'Current Value',
    ];

    $destColumnHeaderNames = [
        'HANDLE_ID' => 'handleId',
        'FIELD_TYPE' => 'fieldType',
        'NAME' => 'name',
        'DESCRIPTION' => 'description',
        'PRODUCT_IMAGE_URL' => 'productImageUrl',
        'COLLECTION' => 'collection',
        'SKU' => 'sku',
        'RIBBON' => 'ribbon',
        'PRICE' => 'price',
        'SURCHARGE' => 'surcharge',
        'VISIBLE' => 'visible',
        'DISCOUNT_MODE' => 'discountMode',
        'DISCOUNT_VALUE' => 'discountValue',
        'INVENTORY' => 'inventory',
        'WEIGHT' => 'weight',
        'COST' => 'cost',
        'PRODUCT_OPTION_NAME_1' => 'productOptionName1',
        'PRODUCT_OPTION_TYPE_1' => 'productOptionType1',
        'PRODUCT_OPTION_DESCRIPTION_1' => 'productOptionDescription1',
        'PRODUCT_OPTION_NAME_2' => 'productOptionName2',
        'PRODUCT_OPTION_TYPE_2' => 'productOptionType2',
        'PRODUCT_OPTION_DESCRIPTION_2' => 'productOptionDescription2',
        'PRODUCT_OPTION_NAME_3' => 'productOptionName3',
        'PRODUCT_OPTION_TYPE_3' => 'productOptionType3',
        'PRODUCT_OPTION_DESCRIPTION_3' => 'productOptionDescription3',
        'PRODUCT_OPTION_NAME_4' => 'productOptionName4',
        'PRODUCT_OPTION_TYPE_4' => 'productOptionType4',
        'PRODUCT_OPTION_DESCRIPTION_4' => 'productOptionDescription4',
        'PRODUCT_OPTION_NAME_5' => 'productOptionName5',
        'PRODUCT_OPTION_TYPE_5' => 'productOptionType5',
        'PRODUCT_OPTION_DESCRIPTION_5' => 'productOptionDescription5',
        'PRODUCT_OPTION_NAME_6' => 'productOptionName6',
        'PRODUCT_OPTION_TYPE_6' => 'productOptionType6',
        'PRODUCT_OPTION_DESCRIPTION_6' => 'productOptionDescription6',
        'ADDITIONAL_INFO_TITLE_1' => 'additionalInfoTitle1',
        'ADDITIONAL_INFO_DESCRIPTION_1' => 'additionalInfoDescription1',
        'ADDITIONAL_INFO_TITLE_2' => 'additionalInfoTitle2',
        'ADDITIONAL_INFO_DESCRIPTION_2' => 'additionalInfoDescription2',
        'ADDITIONAL_INFO_TITLE_3' => 'additionalInfoTitle3',
        'ADDITIONAL_INFO_DESCRIPTION_3' => 'additionalInfoDescription3',
        'ADDITIONAL_INFO_TITLE_4' => 'additionalInfoTitle4',
        'ADDITIONAL_INFO_DESCRIPTION_4' => 'additionalInfoDescription4',
        'ADDITIONAL_INFO_TITLE_5' => 'additionalInfoTitle5',
        'ADDITIONAL_INFO_DESCRIPTION_5' => 'additionalInfoDescription5',
        'ADDITIONAL_INFO_TITLE_6' => 'additionalInfoTitle6',
        'ADDITIONAL_INFO_DESCRIPTION_6' => 'additionalInfoDescription6',
        'CUSTOM_TEXT_FIELD_1' => 'customTextField1',
        'CUSTOM_TEXT_CHAR_LIMIT_1' => 'customTextCharLimit1',
        'CUSTOM_TEXT_MANDATORY_1' => 'customTextMandatory1',
        'BRAND' => 'brand',
    ];

    $sourceRows = [];
    $destRows = [];

    if (($destFileHandle = fopen($destfile, 'w')) === false) {
        exit('Could not open the file for writing: '.$destfile.' - possibly directory does not exist or the server does not have filesystem permissions to read it');
    }

    $sourceColumnHeaderIndexes = [];

    $destColumnHeaders = [
        'handleId',
        'fieldType',
        'name',
        'description',
        'productImageUrl',
        'collection',
        'sku',
        'ribbon',
        'price',
        'surcharge',
        'visible',
        'discountMode',
        'discountValue',
        'inventory',
        'weight',
        'cost',
        'productOptionName1',
        'productOptionType1',
        'productOptionDescription1',
        'productOptionName2',
        'productOptionType2',
        'productOptionDescription2',
        'productOptionName3',
        'productOptionType3',
        'productOptionDescription3',
        'productOptionName4',
        'productOptionType4',
        'productOptionDescription4',
        'productOptionName5',
        'productOptionType5',
        'productOptionDescription5',
        'productOptionName6',
        'productOptionType6',
        'productOptionDescription6',
        'additionalInfoTitle1',
        'additionalInfoDescription1',
        'additionalInfoTitle2',
        'additionalInfoDescription2',
        'additionalInfoTitle3',
        'additionalInfoDescription3',
        'additionalInfoTitle4',
        'additionalInfoDescription4',
        'additionalInfoTitle5',
        'additionalInfoDescription5',
        'additionalInfoTitle6',
        'additionalInfoDescription6',
        'customTextField1',
        'customTextCharLimit1',
        'customTextMandatory1',
        'brand',
    ];

    fputcsv($destFileHandle, $destColumnHeaders);

    $count = 0;
    if (($sourceFileHandle = fopen($sourcefile, 'r')) !== false) {
        while (($sourceRow = fgetcsv($sourceFileHandle)) !== false) {
            // $sourceRows[] = $data;
            if (empty($sourceColumnHeaderIndexes)) {
                // let's assume the first row contains the column names
                $sourceColumnHeaderIndexes = [
                    'AUTHOR' => array_search($sourceColumnHeaderNames['AUTHOR'], $sourceRow),
                    'TITLE' => array_search($sourceColumnHeaderNames['TITLE'], $sourceRow),
                    'ISBN' => array_search($sourceColumnHeaderNames['ISBN'], $sourceRow),
                    'FORMAT' => array_search($sourceColumnHeaderNames['FORMAT'], $sourceRow),
                    'PAGES' => array_search($sourceColumnHeaderNames['PAGES'], $sourceRow),
                    'PUBLISHER' => array_search($sourceColumnHeaderNames['PUBLISHER'], $sourceRow),
                    'PUBLICATION_DATE' => array_search($sourceColumnHeaderNames['PUBLICATION_DATE'], $sourceRow),
                    'GENRE' => array_search($sourceColumnHeaderNames['GENRE'], $sourceRow),
                    'ADDED_DATE' => array_search($sourceColumnHeaderNames['ADDED_DATE'], $sourceRow),
                    'COVER_PRICE' => array_search($sourceColumnHeaderNames['COVER_PRICE'], $sourceRow),
                    'QUANTITY' => array_search($sourceColumnHeaderNames['QUANTITY'], $sourceRow),
                    'CONDITION' => array_search($sourceColumnHeaderNames['CONDITION'], $sourceRow),
                    'SUBJECT' => array_search($sourceColumnHeaderNames['SUBJECT'], $sourceRow),
                    'EDITION' => array_search($sourceColumnHeaderNames['EDITION'], $sourceRow),
                    'PLOT' => array_search($sourceColumnHeaderNames['PLOT'], $sourceRow),
                    'CURRENT_VALUE' => array_search($sourceColumnHeaderNames['CURRENT_VALUE'], $sourceRow),
                ];
                continue;
            }

            // only import if ISBN exists
            if (empty($sourceRow[$sourceColumnHeaderIndexes['ISBN']])) {
                continue;
            }

            // no duplicates allowed
            if (in_array($sourceRow[$sourceColumnHeaderIndexes['ISBN']], $uniqueIsbns)) {
                continue;
            }

            $uniqueIsbns[] = $sourceRow[$sourceColumnHeaderIndexes['ISBN']];

            $descriptionRow = [];

            if (!empty($sourceRow[$sourceColumnHeaderIndexes['AUTHOR']])) {
                $descriptionRow[] = "Author: {$sourceRow[$sourceColumnHeaderIndexes['AUTHOR']]}";
            }
            if (!empty($sourceRow[$sourceColumnHeaderIndexes['PUBLISHER']])) {
                $descriptionRow[] = "Publisher: {$sourceRow[$sourceColumnHeaderIndexes['PUBLISHER']]}";
            }
            if (!empty($sourceRow[$sourceColumnHeaderIndexes['EDITION']])) {
                $descriptionRow[] = "Edition: {$sourceRow[$sourceColumnHeaderIndexes['EDITION']]}";
            }
            if (!empty($sourceRow[$sourceColumnHeaderIndexes['FORMAT']])) {
                $descriptionRow[] = "Format: {$sourceRow[$sourceColumnHeaderIndexes['FORMAT']]}";
            }
            if (!empty($sourceRow[$sourceColumnHeaderIndexes['PAGES']])) {
                $descriptionRow[] = "Pages: {$sourceRow[$sourceColumnHeaderIndexes['PAGES']]}";
            }
            if (!empty($sourceRow[$sourceColumnHeaderIndexes['GENRE']])) {
                $descriptionRow[] = "Genre: {$sourceRow[$sourceColumnHeaderIndexes['GENRE']]}";
            }
            if (!empty($sourceRow[$sourceColumnHeaderIndexes['CONDITION']])) {
                $descriptionRow[] = "Condition: {$sourceRow[$sourceColumnHeaderIndexes['CONDITION']]}";
            }
            if (!empty($sourceRow[$sourceColumnHeaderIndexes['PLOT']])) {
                $descriptionRow[] = "{$sourceRow[$sourceColumnHeaderIndexes['PLOT']]}";
            }
            if (!empty($sourceRow[$sourceColumnHeaderIndexes['ISBN']])) {
                $descriptionRow[] = "ISBN: {$sourceRow[$sourceColumnHeaderIndexes['ISBN']]}";
            }

            $productImageUrl = '';
            if (!empty($sourceRow[$sourceColumnHeaderIndexes['ISBN']])) {
                $productImageUrl = 'https://covers.openlibrary.org/b/isbn/'.$sourceRow[$sourceColumnHeaderIndexes['ISBN']].'-L.jpg';
            }

            $destRow = [
                $sourceRow[$sourceColumnHeaderIndexes['ISBN']] ?? '',
                'Product', // 'FIELD_TYPE' => 'fieldType',
                substr($sourceRow[$sourceColumnHeaderIndexes['TITLE']], 0, 80) ?? '',
                implode('<br />', $descriptionRow),
                $productImageUrl,
                'Books', // 'COLLECTION' => 'collection',
                $sourceRow[$sourceColumnHeaderIndexes['ISBN']] ?? '',
                '', // 'RIBBON' => 'ribbon',
                (float) $sourceRow[$sourceColumnHeaderIndexes['CURRENT_VALUE']] ?? 0,
                '', // 'SURCHARGE' => 'surcharge',
                '', // 'VISIBLE' => 'visible',
                'Percent', // 'DISCOUNT_MODE' => 'discountMode',
                '', // 'DISCOUNT_VALUE' => 'discountValue',
                $sourceRow[$sourceColumnHeaderIndexes['QUANTITY']] ?? '',
                '', // 'WEIGHT' => 'weight',
                '', // 'COST' => 'cost',
            ];

            fputcsv($destFileHandle, $destRow);

            ++$count;
        }
        fclose($sourceFileHandle);
    } else {
        exit('Could not open the file for reading: '.$sourcefile.' - possibly file does not exist or the server does not have filesystem permissions to read it');
    }

    fclose($destFileHandle);

    ob_end_clean();
    header('Content-Type: text/csv');
    header('Content-disposition: attachment; filename="wix-products-'.date('Y-m-d').'.csv"');
    readfile($destfile);
    exit;
} else {
    // show upload form?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        h1, p{
            margin: 1rem;
            padding: 0;
        }
div{
    margin: 1rem;
    padding: 1rem;
    border: solid 1px gray;
    background: lightgray;
}
        </style>
</head>
<body>
    <h1>Convert file from CLZ Books to Wix Stores</h1>

    <p>Use the CSV file that you exported from CLZ Books Cloud: Upload it here and it will give a new CSV file that is formatted for importing to Wix Stores Products</p>


    <form method="post" enctype="multipart/form-data">
        
        <?php if (!empty($password)) { ?>
            <div>
                <label for="password">Password:</label>
                <input id="password" type="password" name="password" />
            </div>
        <?php } ?>
        <div>
            <label for="file">Source File:</label>
            <input id="file" type="file" name="file" />
        </div>
        <div>
            <input type="submit" />
        </div>
    </form>
</body>
</html>

<?php
}
