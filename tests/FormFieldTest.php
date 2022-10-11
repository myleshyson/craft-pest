<?php

it('renders the page with a form')
    ->get('/page-with-basic-form')
    ->assertOk()
    ->form();


it('is unhappy when no form found')
    ->expectExceptionMessage("Unable to select form.")
    ->get('/response-test')
    ->assertOk()
    ->form();


it('can fill a field and collect existing fields', function () {
    $formResponse = $this->get('/page-with-basic-form')
        ->assertOk();
    
    $formResponse->getRequest()
        ->assertMethod('get');

    $submitResponse = $formResponse
        ->fill('second', 'updated value')
        ->submit()
        ->assertOk();

    $submitResponse->getRequest()
        ->assertMethod('post')
        ->assertBody([
            'first' => 'prefilled',
            'second' => 'updated value'
        ]);
});


it('can deal with many forms on one page')->get('/page-with-multiple-forms')
    ->assertOk()
    ->form('#form2');


it('can fill fields with array style names', function () {
    $fields = $this->get('/page-with-multiple-forms')
        ->assertOk()
        ->form('#form3')
        ->fill('row[two]', 'updated')
        ->getFields();

    expect($fields)->toBe([
        'row' => [
            'one' => 'one',
            'two' => 'updated',
            'three' => 'three'
        ]
    ]);
});


it('does not see disabled fields', function () {
    $fields = $this->get('/page-with-multiple-forms')
        ->assertOk()
        ->form('#form4')
        ->getFields();

    // row[one] exists but is disabled
    expect($fields)->toBe([
        'row' => [
            'two' => 'two'
        ]
    ]);
});


it('works with select fields', function () {
    $form = $this->get('/page-with-multiple-forms')
        ->assertOk()
        ->form('#form5');

    $initalState = $form->getFields();
    $selectByName = $form->select('country', 'Ukraine')->getFields();
    $selectByValue = $form->select('country', 'DE')->getFields();

    expect($initalState)->toBe(['country' => '']);
    expect($selectByName)->toBe(['country' => 'UA']);
    expect($selectByValue)->toBe(['country' => 'DE']);

});
