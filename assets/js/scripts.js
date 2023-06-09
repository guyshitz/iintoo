let request;
const REQUEST_FAIL = 500,
    REQUEST_SUCCESS = 200;

// Handles request from forms by ajax
function productRequestHandler(formElm) {
    let method = formElm.attr("method") === "post" ? "post" : "get"; // Determines the request method

    formElm.submit(function(event) {
        event.preventDefault();

        if (request)
            request.abort();

        let $form = $(this);
        let $inputs = $form.find("input:not([type=file]):not(:disabled), button:not(:disabled), select:not(:disabled), textarea:not(:disabled)");

        let formData = new FormData($form[0]);

        if(event.originalEvent !== undefined)
            formData.append(event.originalEvent.submitter.getAttribute('name'), event.originalEvent.submitter.value);

        // Disabling all inputs until response arrives
        $inputs.prop("disabled", true);

        request = $.ajax({
            method: method,
            processData: false,
            contentType: false,
            cache: false,
            data: formData,
            url: "request/product/create.php"
        });

        request.done(function(response) {
            $("html, body").animate({ scrollTop: 0 }, "fast");

            if(response.code === REQUEST_FAIL)
                $('.response-error').html('<div class="alert danger">' + response['msg'] + '</div>');
            else if(response.code === REQUEST_SUCCESS) {
                // Adding the product after it has been added successfully

                let form = $('.product-form');
                let productImgElm = form.find('.product-img');
                let productImg = productImgElm.attr('src');

                let title = $('#product-title')[0].value; // Better replacement than .val() by performance
                let description = $('#product-description')[0].value; // Better replacement than .val() by performance
                let price = $('#product-price').val();
                let salePrice = $('#sale-price').val();
                let onSale = $('#product-on-sale').is(':checked') ? ' on-sale-badge' : '';
                if(salePrice.length > 0)
                    salePrice = '<span class="product-sale-price">' + salePrice + '</span>';

                let features = form.find('.product-feature');
                let featuresStr = "";
                if(features.length > 0) {
                    features.each(function() {
                        let featureName = $(this).find('.feature-name').val();
                        let featureVal = $(this).find('.feature-value').val();

                        if(featureName.length > 0 && featureVal.length > 0)
                            featuresStr += '<dt>' + featureName + '</dt>'
                                + '<dd>' + featureVal + '</dt>';
                    });

                    if(featuresStr.length > 0)
                        featuresStr = '<dl>' + featuresStr + '</dl>';
                }

                // Adding the inserted product card HTML to the products list
                $(`<li class="product-card">
                <div class="product-card-container${onSale}">
                    <div class="product-card-body">
                        <img src="${productImg}" alt="${title}">
                        <h3>${title}</h3>
                        <div class="product-price">
                            <span class="product-price-symbol">$</span>
                            <span class="product-price-value">${price}</span>
                            <span class="product-sale-price">$${salePrice}</span>
                        </div>
                        <p>${description}</p>
                        ${featuresStr}
                    </div>
                </div>
            </li>`).prependTo('.product-list');

                $('.modal').fadeOut();
                let toast = $('.toast');

                // Updates the response message in toast
                toast.find('.message .toast-msg').html(response['msg']);

                // Enables toast
                toast.addClass('active');
                toast.find('.progress').addClass('active');

                toast.find('.toast-close').on('click', function() {
                    toast.removeClass('active');
                });

                setTimeout(function() {
                    toast.removeClass('active');
                    toast.find('.progress').removeClass('active');
                }, 35000);

                // Clears form data after successful response

                // Clear inputs
                $form.find('input,textarea').val("");
                $form.find('input[type="checkbox"]').prop('checked', false);

                // Clears previous responses
                $('.response-error').html("");

                // Clears clone-block behaviour
                $('.clone-block').first().nextAll('.clone-block').remove();
                $('.clone-block .remove-btn').attr("disabled", true);

                // Sets product image preview to default
                productImgElm.attr("src", "assets/images/products/placeholder.jpg");
            }
        });

        request.fail(function(jqXHR, textStatus, errorThrown) {
            // Response failed. Time to debug
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            console.log(jqXHR.responseText);
        });

        request.always(function(e) {
            $inputs.prop("disabled", false);
        });

    });
}

// Inits the clone block remove button events
function initCloneRemoveEvent(cloneBlockElm) {
    let removeCloneBtn = cloneBlockElm.find('.remove-btn');

    removeCloneBtn.attr('disabled', false);

    removeCloneBtn.on('click', function() {
        cloneBlockElm.slideUp("normal", function() {
            $(this).remove();
            let cloneBlock = $('.clone-block');
            if(cloneBlock.length === 1) {
                // One clone block left. It's time to disable its removal option
                let lastCloneRemove = cloneBlock.find('.remove-btn');
                lastCloneRemove.attr("disabled", true); // Disabling the button
                lastCloneRemove.off(); // Removing current events to let new ones be created
            }
        } );
    });
}

$(document).ready(function() {
    /** Shows a modal on add product button click **/
    $('.product-btn').on('click', function(e) {
        e.preventDefault();
        let modal = $('.modal');

        modal.fadeIn(200);

        // Closing the modal on icon click or anywhere else out of the modal by overlay
        $('.modal-close,.modal-overlay').on('click', function() {
            modal.fadeOut(200);
        })
    });

    /** Handles the product creation request **/
    productRequestHandler($('.product-form'));

    /** Clone Block **/
    let cloneBlockBtn = $('.clone-block-btn');

    if(cloneBlockBtn.length > 0) {
        cloneBlockBtn.each(function() {
            $(this).on('click', function() {
                let cloneBlock = $(this).parent().nextAll('.clone-block:last');

                initCloneRemoveEvent(cloneBlock);
                let newClone = cloneBlock.clone().hide().insertAfter(cloneBlock).slideDown("normal");
                initCloneRemoveEvent(newClone);
            });
        })
    }

    /** Image Upload Preview **/
    let imgField = $('.img-field');
    if(imgField.length > 0) {
        imgField.each(function() {
            let img = $(this).find('img');
            let inp = $(this).find('input');
            if(img.length > 0 && inp.length > 0) {
                $(this).find('a').on('click', function() {
                    $(inp.get(0)).trigger('click');
                });

                inp.change(function() {
                    let file = this.files[0];
                    let reader = new FileReader();
                    // Set preview image into the popover data-content
                    reader.onload = function (e) {
                        img.attr('src', e.target.result);
                    }

                    if(this.files[0])
                        reader.readAsDataURL(file);
                });
            }
        });
    }
});