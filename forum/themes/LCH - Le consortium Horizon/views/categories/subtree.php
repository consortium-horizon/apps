<?php

$Category = $this->data('Category');
if (!$Category)
    return;

$SubCategories = CategoryModel::MakeTree(CategoryModel::categories(), $Category);

if (!$SubCategories)
    return;

require_once $this->fetchViewLocation('helper_functions', 'categories', 'vanilla');

WriteCategoryTable($SubCategories, 2);

?>
