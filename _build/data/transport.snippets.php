<?php
$snippets = array();
 
$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'jShop',
    'description' => 'Basic Shopping Component for MODx.',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.jshop.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.jshop.php';
$snippets[0]->setProperties($properties);
unset($properties);

$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'js.listCategories',
    'description' => 'Category Listing snippet for jShop Items',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.js.listcategories.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.js.listCategories.php';
$snippets[1]->setProperties($properties);
unset($properties);

$snippets[2]= $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => 2,
    'name' => 'js.paymentMethods',
    'description' => 'Snippet for listing saved payment methods',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.js.paymentMethods.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.js.paymentMethods.php';
$snippets[2]->setProperties($properties);
unset($properties);

$snippets[3]= $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
    'id' => 3,
    'name' => 'js.emptyShop',
    'description' => 'Snippet for emptying the cart upon successful completion.',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.js.emptyShop.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.js.emptyShop.php';
$snippets[3]->setProperties($properties);
unset($properties);

$snippets[4]= $modx->newObject('modSnippet');
$snippets[4]->fromArray(array(
    'id' => 4,
    'name' => 'js.prefillFields',
    'description' => 'Formit Hook for prefilling fields for logged in users.',
    'snippet' => getSnippetContent($sources['hooks'].'prefilluser.hook.php'),
),'',true,true);

$snippets[5]= $modx->newObject('modSnippet');
$snippets[5]->fromArray(array(
    'id' => 5,
    'name' => 'js.createUser',
    'description' => 'Formit Hook for creating user.',
    'snippet' => getSnippetContent($sources['hooks'].'createUser.hook.php'),
),'',true,true);

$snippets[6]= $modx->newObject('modSnippet');
$snippets[6]->fromArray(array(
    'id' => 6,
    'name' => 'js.createOrder',
    'description' => 'Formit Hook for creating an order object.',
    'snippet' => getSnippetContent($sources['hooks'].'createOrder.hook.php'),
),'',true,true);

$snippets[7]= $modx->newObject('modSnippet');
$snippets[7]->fromArray(array(
    'id' => 7,
    'name' => 'js.createCharge',
    'description' => 'Formit Hook for creating a Stripe charge.',
    'snippet' => getSnippetContent($sources['hooks'].'createCharge.hook.php'),
),'',true,true);

$snippets[8]= $modx->newObject('modSnippet');
$snippets[8]->fromArray(array(
    'id' => 8,
    'name' => 'js.checkPayment',
    'description' => 'Stripe WebHook for validating successful payment',
    'snippet' => getSnippetContent($sources['hooks'].'payment.webhook.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.payment.webhook.php';
$snippets[8]->setProperties($properties);
unset($properties);

$snippets[9]= $modx->newObject('modSnippet');
$snippets[9]->fromArray(array(
    'id' => 9,
    'name' => 'js.validateShipping',
    'description' => 'Validation Hook for making sure that shipping values are set properly.',
    'snippet' => getSnippetContent($sources['hooks'].'validateShipping.hook.php'),
),'',true,true);

$snippets[10]= $modx->newObject('modSnippet');
$snippets[10]->fromArray(array(
    'id' => 10,
    'name' => 'formatPrice',
    'description' => 'Output Modifier to format prices',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.js.formatPrice.php'),
),'',true,true);

$snippets[11]= $modx->newObject('modSnippet');
$snippets[11]->fromArray(array(
    'id' => 11,
    'name' => 'getStatusName',
    'description' => 'Output Modifier to replace id with status name',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.js.getStatusName.php'),
),'',true,true);

$snippets[12]= $modx->newObject('modSnippet');
$snippets[12]->fromArray(array(
    'id' => 12,
    'name' => 'js.getOrderList',
    'description' => 'Order listing snippet for customer account page.',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.js.getOrderList.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.js.getOrderList.php';
$snippets[12]->setProperties($properties);
unset($properties);

$snippets[13]= $modx->newObject('modSnippet');
$snippets[13]->fromArray(array(
    'id' => 13,
    'name' => 'js.validatePricing',
    'description' => 'Validation Hook for making sure that the pricing wasn\'t manipulated.',
    'snippet' => getSnippetContent($sources['hooks'].'validatePricing.hook.php'),
),'',true,true);

$snippets[14]= $modx->newObject('modSnippet');
$snippets[14]->fromArray(array(
    'id' => 14,
    'name' => 'js.getSavedPaymentMethods',
    'description' => 'Saved Payment Method listing snippet for customer account page.',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.js.getSavedPaymentMethods.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.js.getSavedPaymentMethods.php';
$snippets[14]->setProperties($properties);
unset($properties);

$snippets[15]= $modx->newObject('modSnippet');
$snippets[15]->fromArray(array(
    'id' => 15,
    'name' => 'js.removePaymentMethod',
    'description' => 'Formit Hook for removing saved payment methods from the user object.',
    'snippet' => getSnippetContent($sources['hooks'].'removePaymentMethod.hook.php'),
),'',true,true);

return $snippets;