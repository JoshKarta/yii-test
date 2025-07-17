<?php

use kartik\sortable\Sortable;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\jui\JuiAsset;

// Register jQuery UI
JuiAsset::register($this);

$this->title = 'Sort Menu Items';
$this->params['breadcrumbs'][] = ['label' => 'Menu Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Define the update URL before using it
$updateOrderUrl = Url::to(['update-order']);

// Function to render menu items recursively
function renderMenuItem($item)
{
    $icon = $item->icon ? '<i data-lucide="' . $item->icon . '" style="width: 18px; height: 18px;" class="me-2"></i>' : '';
    $childCount = count($item->children);

    $html = '<div class="list-group-item d-flex align-items-center" data-id="' . $item->id . '">';
    $html .= '<div class="ms-2 flex-grow-1">' . $icon . $item->label . '</div>';

    if ($childCount > 0) {
        $html .= '<div class="children-container ms-4 w-100">';
        foreach ($item->children as $child) {
            $html .= renderMenuItem($child);
        }
        $html .= '</div>';
    }

    $html .= '</div>';
    return $html;
}
?>

<div class="menu-item-sort">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Drag and drop items to reorder</h3>
        </div>
        <div class="card-body">
            <div id="sortable-list" class="list-group">
                <?php foreach ($items as $item): ?>
                    <?= renderMenuItem($item) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
$css = <<<CSS
    .loading {
        opacity: 0.5;
        pointer-events: none;
    }
    .list-group-item {
        cursor: move;
        margin-bottom: 5px;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .children-container {
        padding-left: 20px;
        border-left: 2px solid #dee2e6;
        margin-top: 10px;
    }
    .placeholder {
        border: 2px dashed #dee2e6;
        background-color: #f8f9fa;
        margin: 5px 0;
        height: 40px;
    }
CSS;

$js = <<<JS
    // Initialize Lucide icons
    // lucide.createIcons();
    
    // Initialize Toast configuration
    // const Toast = Swal.mixin({
    //     toast: true,
    //     position: 'top-end',
    //     showConfirmButton: false,
    //     timer: 3000,
    //     timerProgressBar: true,
    //     didOpen: (toast) => {
    //         toast.addEventListener('mouseenter', Swal.stopTimer)
    //         toast.addEventListener('mouseleave', Swal.resumeTimer)
    //     }
    // });
    
    function getMenuStructure() {
        let structure = [];
        
        // Function to process each item and its children
        function processItem(element, parentId = null) {
            return {
                id: $(element).data('id'),
                parent_id: parentId,
                sort_order: structure.length + 1
            };
        }
        
        // Process all items
        $('#sortable-list .list-group-item').each(function() {
            let item = $(this);
            let parentItem = item.parent().closest('.list-group-item');
            let parentId = parentItem.length ? parentItem.data('id') : null;
            
            structure.push(processItem(item, parentId));
        });
        
        return structure;
    }
    
    // Initialize nested sortable
    $('#sortable-list, .children-container').sortable({
        cursor: 'move',
        placeholder: 'placeholder',
        connectWith: '.children-container, #sortable-list',
        tolerance: 'pointer',
        update: function(event, ui) {
            // Only trigger on the receiving list's update event
            if (this === ui.item.parent()[0]) {
                let structure = getMenuStructure();
                
                // Show loading state
                $('.menu-item-sort .card-body').addClass('loading');
                
                $.ajax({
                    url: '$updateOrderUrl',
                    type: 'POST',
                    data: {
                        items: structure,
                        _csrf: yii.getCsrfToken()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Toast.fire({
                            //     icon: 'success',
                            //     title: response.message
                            // });
                            alert("Success")
                        } else {
                            alert("Error")
                            // Toast.fire({
                            //     icon: 'error',
                            //     title: response.message
                            // });
                        }
                    },
                    error: function() {
                        alert("Eroor")
                        // Toast.fire({
                        //     icon: 'error',
                        //     title: 'An error occurred while updating the order'
                        // });
                    },
                    complete: function() {
                        // Remove loading state
                        $('.menu-item-sort .card-body').removeClass('loading');
                    }
                });
            }
        }
    }).disableSelection();
JS;

$this->registerCss($css);
$this->registerJs($js);
?>