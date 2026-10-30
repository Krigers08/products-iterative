<?php

test('the application redirects to the product index', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('products.index'));
});
