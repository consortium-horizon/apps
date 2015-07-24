<?php defined('APPLICATION') or exit();

$ControllerName = strtolower($this->_Sender->ControllerName);
$RequestMethod = strtolower($this->_Sender->RequestMethod);

$OnArticlesController = ($ControllerName === 'articlescontroller');
$OnAllCategoriesMethod = ($OnArticlesController && ($RequestMethod === 'categories'));

$Categories = $this->Data->Result();
$CurrentCategoryID = val('ArticleCategoryID', $this->_Sender->Data('ArticleCategory'), false);
?>
<div class="Box BoxArticleCategories">
    <h4><?php echo T('Article Categories'); ?></h4>

    <?php if (!C('Articles.Modules.ShowCategoriesAsDropDown', false)): ?>
        <ul class="PanelInfo PanelArticleCategories">
            <?php
            // All Categories link
            $AllArticlesClass = $OnAllCategoriesMethod ? array('class' => 'Active') : '';
            echo Wrap(Anchor(T('All Categories'), '/articles/categories'), 'li', $AllArticlesClass);

            $ArticleModel = new ArticleModel();
            $ArticleOffset = 0;
            $ArticleLimit = 1;
            $ArticleWheres = array('a.Status' => ArticleModel::STATUS_PUBLISHED);
            foreach ($Categories as $Category) {
                // Category must have at least one published article.
                $ArticleWheres['a.ArticleCategoryID'] = $Category->ArticleCategoryID;
                $Article = $ArticleModel->Get($ArticleOffset, $ArticleLimit, $ArticleWheres)->FirstRow();
                $PublishedArticleExists = isset($Article->ArticleID);

                if (!$PublishedArticleExists)
                    continue;

                // Output category link
                $CategoryClass = ($CurrentCategoryID === $Category->ArticleCategoryID) ? array('class' => 'Active') :
                    '';
                echo Wrap(Anchor($Category->Name, ArticleCategoryUrl($Category)), 'li', $CategoryClass);
            }
            ?>
        </ul>
    <?php else: ?>
        <select id="ArticleCategoriesDropDown">
            <option disabled<?php echo(!$CurrentCategoryID ? ' selected' :
                ''); ?>><?php echo T('Select a category...'); ?></option>
            <option id="ArticleCategoriesDropDown_AllCategories" value="all"<?php echo($OnAllCategoriesMethod ?
                ' selected ' : ''); ?>><?php echo T('All Categories'); ?></option>
            <?php foreach ($Categories as $Category): ?>
                <option id="ArticleCategoriesDropDown_ArticleCategory_<?php echo $Category->ArticleCategoryID; ?>"
                        value="<?php echo $Category->UrlCode; ?>"<?php echo(($CurrentCategoryID === $Category->ArticleCategoryID) ?
                    ' selected' : ''); ?>><?php echo $Category->Name; ?></option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
</div>