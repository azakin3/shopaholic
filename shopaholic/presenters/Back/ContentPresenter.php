<?php
final class Back_ContentPresenter extends Back_BasePresenter
{
    public function actionPriceChanges($product_id)
    {
        if (!($this->template->product = mapper::products()->findById($product_id))) {
            $this->redirect('products');
            $this->terminate();
            return ;
        }
        $this->template->changes = mapper::products()->findPriceChanges($product_id);
    }

    public function actionDeletePage($nice_name)
    {
        mapper::pages()->deleteOne($nice_name);
        $this->redirect('pages');
        $this->terminate();
    }

    public function actionEditPage($nice_name)
    {
        $this->template->page = mapper::pages()->findByNiceName($nice_name)->__toArray();
    }

    public function actionDeleteManufacturer($nice_name)
    {
        $manufacturer = mapper::manufacturers()->findByNiceName($nice_name);
        if ($manufacturer) {
            mapper::manufacturers()->deleteOne($manufacturer);
        }
        $this->redirect('manufacturers');
        $this->terminate();
    }

    public function actionEditManufacturer($nice_name)
    {
        $this->template->manufacturer = mapper::manufacturers()->findByNiceName($nice_name)->__toArray();
        $this->template->manufacturer['content'] = $this->template->manufacturer['description'];
    }

    public function actionEditCategory($id)
    {
        if (($this->template->category = mapper::categories()->findById($id)) === NULL) {
            $this->redirect('categories');
            $this->terminate();
            return ;
        }
        $this->template->category = $this->template->category->__toArray();
        $this->template->category['content'] = $this->template->category['description'];
    }

    public function actionAddCategory($id)
    {
        $id = intval($id);
        $this->template->id = $id;
    }

    public function actionDeletePicture($id)
    {
        $id = intval($id);
        mapper::pictures()->deleteOne($id);
        $this->redirect('pictures');
        $this->terminate();
    }

    public function actionDeleteProduct($nice_name)
    {
        $product = mapper::products()->findByNiceName($nice_name);
        if ($product) {
            mapper::products()->deleteOne($product);
        }
        $this->redirect('products');
        $this->terminate();
    }

    public function actionEditProduct($nice_name)
    {
        $this->template->product = mapper::products()->findByNiceName($nice_name)->__toArray();
        $this->template->product['content'] = $this->template->product['description'];
    }

    public function actionEditAvailability($id)
    {
        $this->template->id = intval($id);
    }

    public function renderPriceChanges()
    {
        $this->template->title = $this->template->product->getName() . ' – ' . __('Price changes');
    }

    public function renderPages()
    {
        $this->template->title = __('Pages');
        $this->template->pages = mapper::pages()->findNotRef();
    }

    public function renderAddPage()
    {
        $this->template->title = __('Add page');
        $this->template->form = $this->getComponent('pageAddForm');
    }

    public function renderEditPage()
    {
        $this->template->title = $this->template->page['name'] . ' – ' . __('Edit page');
        $this->template->form = $this->getComponent('pageEditForm');
        $this->template->form->setDefaults($this->template->page);
        if ($this->template->page['picture'] !== NULL &&
            $this->template->form['picture_id']->getValue() === NULL)
        {
            $this->template->form['picture_id']->setValue($this->template->page['picture']->getId());
        }
    }

    public function renderManufacturers()
    {
        $this->template->title = __('Manufacturers');
        $this->template->manufacturers = mapper::manufacturers()->findAll();
    }

    public function renderAddManufacturer()
    {
        $this->template->title = __('Add manufacturer');
        $this->template->form = $this->getComponent('manufacturerAddForm');
    }

    public function renderEditManufacturer()
    {
        $this->template->title = $this->template->manufacturer['name'] . ' – ' . __('Edit manufacturer');
        $this->template->form = $this->getComponent('manufacturerEditForm');
        $this->template->form->setDefaults($this->template->manufacturer);
        if ($this->template->manufacturer['picture'] !== NULL &&
            $this->template->form['picture_id']->getValue() === NULL)
        {
            $this->template->form['picture_id']->setValue($this->template->manufacturer['picture']->getId());
        }
    }

    public function renderCategories()
    {
        $this->template->title = __('Categories');
        $this->template->form = $this->getComponent('categoriesForm');
    }

    public function renderAddCategory()
    {
        $this->template->title = __('Add category');
        $this->template->form = $this->getComponent('categoryAddForm');
        $this->template->form->setDefaults(array('id' => $this->template->id));
    }

    public function renderEditCategory()
    {
        $this->template->title = $this->template->category['name'] . ' – ' . __('Edit category');
        $this->template->form = $this->getComponent('categoryEditForm');
        $this->template->form->setDefaults($this->template->category);
        if ($this->template->category['picture'] !== NULL &&
            $this->template->form['picture_id']->getValue() === NULL)
        {
            $this->template->form['picture_id']->setValue($this->template->category['picture']->getId());
        }
    }

    public function renderPictures()
    {
        $this->template->title = __('Pictures');
        $this->template->pictures = mapper::pictures()->findAll();
        $this->template->form = $this->getComponent('newPictureForm');
        $this->template->registerHelper('remove_ext', array(__CLASS__, 'remove_ext'));
    }

    public function renderProducts($page_number = 0)
    {
        $this->template->title = __('Products');

        $this->template->page_number = $page_number;
        $this->template->paginator = new Paginator;
        $this->template->paginator->setItemCount(
            mapper::products()->countAll());
        $this->template->paginator->setItemsPerPage(
            Environment::getVariable('itemsPerPage', 30));
        $this->template->paginator->setPage($this->template->page_number);

        $this->template->products = mapper::products()->findAll(
            $this->template->paginator->getLength(),
            $this->template->paginator->getOffset());
    }

    public function renderAddProduct()
    {
        $this->template->title = __('Add product');
        $this->template->form = $this->getComponent('productAddForm');
    }

    public function renderEditProduct()
    {
        $this->template->title = $this->template->product['name'] . ' – ' . __('Edit product');
        $this->template->form = $this->getComponent('productEditForm');
        $this->template->form->setDefaults($this->template->product);
        if ($this->template->product['picture'] !== NULL &&
            $this->template->form['picture_id']->getValue() === NULL)
        {
            $this->template->form['picture_id']->setValue($this->template->product['picture']->getId());
        }
        if ($this->template->form['category_id']->getValue() === NULL) {
            $category = mapper::products()->findCategoryOf($this->template->product['id']);
            if ($category !== NULL) {
                $this->template->form['category_id']->setValue($category->getId());
            }
        }
        if ($this->template->form['manufacturer_id']->getValue() === NULL) {
            $manufacturer = mapper::products()->findManufacturerOf($this->template->product['id']);
            if ($manufacturer !== NULL) {
                $this->template->form['manufacturer_id']->setValue($manufacturer->getId());
            }
        }
        if ($this->template->form['availability_id']->getValue() === NULL) {
            if ($this->template->product['availability'] !== NULL) {
                $this->template->form['availability_id']->setValue($this->template->product['availability']->getId());
            }
        }
    }

    public function renderAddAvailability()
    {
        $this->template->title = __('Add availavility');
        $this->template->form = $this->getComponent('availabilityAddForm');
    }

    public function renderEditAvailability()
    {
        $this->template->form = $this->getComponent('availabilityEditForm');
        $availability = mapper::product_availabilities()->findById($this->template->id);
        if ($availability !== NULL) {
            $this->template->title = $availability->getName() . ' – ' . __('Edit availability');
            $this->template->form->setDefaults($availability->__toArray());
        }
    }

    public function renderAvailabilities()
    {
        $this->template->title = __('Availabilities');
        $this->template->form = $this->getComponent('availabilitiesForm');
    }

    public function renderTitlePage()
    {
        $this->template->title = __('Title page');
        $this->template->form = $this->getComponent('titlePageForm');
    }

    public function createComponent($name)
    {
        switch ($name) {
            case 'pageAddForm':
            case 'pageEditForm':
                if (!isset($prefix_len)) {
                    $prefix_len = strlen('page');
                }
            case 'manufacturerAddForm':
            case 'manufacturerEditForm':
                if (!isset($prefix_len)) {
                    $prefix_len = strlen('manufacturer');
                }
            case 'categoryAddForm':
            case 'categoryEditForm':
                if (!isset($prefix_len)) {
                    $prefix_len = strlen('category');
                }
            case 'productAddForm':
            case 'productEditForm':
                if (!isset($prefix_len)) {
                    $prefix_len = strlen('product');
                }
                $action = substr($name, $prefix_len);
                $action = substr($action, 0, strlen($action) - 4);

                // instantiate
                $form = new AppForm($this, $name);

                // name
                $form->addText('name', __('Name:'))
                    ->addRule(Form::FILLED, __('You have to entry name.'));

                // edit or add?
                if (substr($name, strlen($name) - 8) !== 'EditForm') { // add
                    $form->addText('nice_name', __('Name in URL:'))
                        ->addRule(Form::FILLED, __('You have to entry URL name.'));
                } else { // edit
                    $form->addHidden('nice_name');
                    if (strncmp($name, 'manufacturer', 12) === 0 || strncmp($name, 'product', 7) === 0) {
                        $form->addHidden('id');
                    }
                }

                // is category?
                if (strncmp($name, 'category', 8) === 0) {
                    $form->addHidden('id');
                }


                // metas
                $form->addText('meta_keywords', __('META keywords:'));
                $form->addText('meta_description', __('META description:'));

                // description or content?
                if (strncmp($name, 'manufacturer', 12) === 0 || strncmp($name, 'category', 8) === 0) {
                    $form->addTextarea('content', __('Description:'));
                } else {
                    $form->addTextarea('content', __('Content:'));
                }

                // is product?
                if (strncmp($name, 'product', 7) === 0) {
                    // category
                    $categories = array(0 => __('–––'));
                    foreach (mapper::categories()->findAll() as $_) {
                        list($depth, $category) = $_;
                        $categories[$category->getId()] = str_repeat('–', $depth) . ' '  . $category->getName();
                    }
                    $form->addSelect('category_id', __('Category:'), $categories)->skipFirst();

                    // manufacturer
                    $manufacturers = array(0 => __('–––'));
                    foreach (mapper::manufacturers()->findAll() as $manufacturer) {
                        $manufacturers[$manufacturer->getId()] = $manufacturer->getName();
                    }
                    $form->addSelect('manufacturer_id', __('Manufacturer:'), $manufacturers)->skipFirst();

                    // availability
                    $availabilities = array(0 => __('–––'));
                    foreach (mapper::product_availabilities()->findAll() as $_) {
                        $availabilities[$_->getId()] = $_->getName();
                    }
                    $form->addSelect('availability_id', __('Availability:'), $availabilities)->skipFirst();
                    
                    // code
                    $form->addText('code', __('Code:'));
                    
                    // price
                    $form->addText('price', __('Price:'))
                        ->addRule(Form::NUMERIC, __('Price has to be a number.'));
                }

                // picture
                $pictures = array(0 => __('–––'));
                foreach (mapper::pictures()->findAll() as $_) {
                    $pictures[$_->getId()] = self::remove_ext($_->getFile());
                }
                $form->addSelect('picture_id', __('Picture:'), $pictures);

                // submit
                $form->addSubmit('ok', '✔ ' . __($action));
                $form->onSubmit[] = array($this, 
                        'on' . ucfirst(substr($name, 0, strlen($name) - 4)) . 'Submit');
            break;

            case 'categoriesForm':
                $form = new AppForm($this, $name);
                $items = array(0 => __('–––'));
                foreach (mapper::categories()->findAll() as $_) {
                    list($depth, $category) = $_;
                    $items[$category->getId()] = str_repeat('–', $depth) . ' '  . $category->getName();
                }
                $form->addSelect('id', __('Category:'), $items)->skipFirst();
                $form->addSubmit('edit', '✎ ' . __('Edit'))
                    ->onClick[] = array($this, 'onCategoriesFormEdit');
                $form->addSubmit('add', '⊕ ' . __('Add subcategory'))
                    ->onClick[] = array($this, 'onCategoriesFormAdd');
                $form->addSubmit('delete', '⊗ ' . __('Delete'))
                    ->onClick[] = array($this, 'onCategoriesFormDelete');
            break;

            case 'newPictureForm':
                $form = new AppForm($this, $name);
                $form->addFile('file', __('File:'));
                $form->addText('rename', __('Rename to:'));
                $form->addText('description', __('Description:'));
                $form->addSubmit('ok', '➦ ' . __('Upload'));
                $form->onSubmit[] = array($this, 'onNewPictureSubmit');
            break;

            case 'availabilitiesForm':
                $form = new AppForm($this, $name);
                $availabilities = array(0 => __('–––'));
                foreach (mapper::product_availabilities()->findAll() as $_) {
                    $availabilities[$_->getId()] = $_->getName();
                }
                $form->addSelect('id', __('Availability:'), $availabilities)->skipFirst();
                $form['id']->addRule(Form::FILLED, __('You have to choose availability.'));
                $form->addSubmit('edit', '✎ ' . __('Edit'))
                    ->onClick[] = array($this, 'onAvailabilitiesFormEdit');
                $form->addSubmit('delete', '⊗ ' . __('Delete'))
                    ->onClick[] = array($this, 'onAvailabilitiesFormDelete');
            break;

            case 'availabilityAddForm':
            case 'availabilityEditForm':
                if (!isset($prefix_len)) {
                    $prefix_len = strlen('availability');
                }
                $action = substr($name, $prefix_len);
                $action = substr($action, 0, strlen($action) - 4);
                $form = new AppForm($this, $name);
                if (substr($name, strlen($name) - 8) === 'EditForm') {
                    $form->addHidden('id');
                }
                $form->addText('name', __('Name:'));
                $form->addSubmit('ok', '✔ ' . __($action));
                $form->onSubmit[] = array($this,
                        'on' . ucfirst(substr($name, 0, strlen($name) - 4)) . 'Submit');
            break;

            case 'titlePageForm':
                $form = new AppForm($this, $name);
                $form->addTextarea('content', __('Content:'))
                    ->addRule(Form::FILLED, __('You have to entry content.'));
                $form->addSubmit('save', '✔ ' . __('Save'));
                $form->onSubmit[] = array($this, 'onTitlePageFormSubmit');
                $form->setDefaults(array(
                    'content' => require_once Environment::expand('%titlePageFile%')
                ));
            break;

            default:
                parent::createComponent($name);
        }
    }

    public function onPageAddSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        if (mapper::pages()->insertOne($form->getValues())) {
            $this->redirect('pages');
            $this->terminate();
            return;
        }

        $form->addError(__('Cannot add page.'));
    }

    public function onPageEditSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        mapper::pages()->updateOne($form->getValues());
        $this->redirect('pages');
        $this->terminate();
    }

    public function onManufacturerAddSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        if (mapper::manufacturers()->insertOne($form->getValues())) {
            $this->redirect('manufacturers');
            $this->terminate();
            return;
        }

        $form->addError(__('Cannot add manufacturer.'));
    }

    public function onManufacturerEditSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        mapper::manufacturers()->updateOne($form->getValues());
        $this->redirect('manufacturers');
        $this->terminate();
    }

    public function onCategoriesFormEdit(SubmitButton $b)
    {
        $values = $this->getComponent('categoriesForm')->getValues();
        $this->redirect('editCategory', $values['id']);
        $this->terminate();
    }

    public function onCategoriesFormAdd(SubmitButton $b)
    {
        $values = $this->getComponent('categoriesForm')->getValues();
        $this->redirect('addCategory', $values['id']);
        $this->terminate();
    }

    public function onCategoriesFormDelete(SubmitButton $b)
    {
        $values = $this->getComponent('categoriesForm')->getValues();
        if ($values['id'] != 0) {
            mapper::categories()->deleteOne($values['id']);
        }
        $this->redirect('categories');
        $this->terminate();
    }

    public function onCategoryAddSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        $values = $form->getValues();
        $parent_id = intval($values['id']);
        if ($parent_id === 0) $parent_id = NULL;
        unset($values['id']);

        mapper::categories()->addOne($values, $parent_id);
        $this->redirect('categories');
        $this->terminate();
    }

    public function onCategoryEditSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        mapper::categories()->updateOne($form->getValues());
        $this->redirect('categories');
        $this->terminate();
    }

    public function onNewPictureSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        $file = $form['file']->getValue();

        // save thumbnail
        try {
            $image = $file->getImage();
        } catch (Exception  $e) {
            $form->addError(__('Uploaded file has to be an image.'));
            return ;
        }

        if ($image->getWidth() > $image->getHeight()) {
            $image->resize(NULL, 60);
        } else {
            $image->resize(60, NULL);
        }
        $image->crop(intval(($image->getWidth() - 60) / 2),
            intval(($image->getHeight() - 60) / 2), 60, 60);
        $thumbnail_filename = sha1(microtime()) . '.png';
        $image->save(Environment::expand('%mediaDir%/' . $thumbnail_filename));
        unset($image);

        // save big picture
        $big_filename = String::webalize($form['rename']->getValue());
        if (empty($big_filename)) {
            $big_filename = $file->getName();
        }
        if (($pos = strrpos($big_filename, '.')) !== FALSE) {
            $ext = substr($big_filename, $pos);
            $before_ext = substr($big_filename, 0, $pos);
            if (strtolower($ext) !== '.png') {
                $big_filename = $before_ext . '.png';
            } else {
                $need_resave = FALSE;
            }
        } else {
            $big_filename .= '.png';
        }

        /*if ($need_resave) {
            $image = $file->getImage();
            $image->save(Environment::expand('%mediaDir%/' . $big_filename));
        } else {
            $file->move(Environment::expand('%mediaDir%/' . $big_filename));
        }*/
        $image = $file->getImage();
        $image->resize(300, 300, Image::ENLARGE);
        $image->save(Environment::expand('%mediaDir%/' . $big_filename));

        // save to db
        if (!mapper::pictures()->insertOne($big_filename, $thumbnail_filename, $form['description']->getValue())) {
            $form->addError(__('Cannot save image data into the database, try again.'));
        } else {
            $this->redirect('pictures');
            $this->terminate();
        }
    }

    public function onProductAddSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        mapper::products()->insertOne($form->getValues());
        $this->redirect('products');
        $this->terminate();
    }

    public function onProductEditSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        mapper::products()->updateOne($form->getValues());
        $this->redirect('products');
        $this->terminate();
    }

    public function onAvailabilitiesFormEdit(SubmitButton $b)
    {
        $form = $this->getComponent('availabilitiesForm');
        $this->redirect('editAvailability', $form['id']->getValue());
        $this->terminate();
    }

    public function onAvailabilitiesFormDelete(SubmitButton $b)
    {
        $form = $this->getComponent('availabilitiesForm');
        mapper::product_availabilities()->deleteOne($form['id']->getValue());
        $this->redirect('availabilities');
        $this->terminate();
    }

    public function onAvailabilityAddSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        mapper::product_availabilities()->insertOne($form->getValues());
        $this->redirect('availabilities');
        $this->terminate();
    }

    public function onAvailabilityEditSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        mapper::product_availabilities()->updateOne($form->getValues());
        $this->redirect('availabilities');
        $this->terminate();
    }

    public static function remove_ext($filename)
    {
        return preg_replace('~\..*$~', '', $filename);
    }

    public function onTitlePageFormSubmit(Form $form)
    {
        if (!$form->isValid()) {
            return ;
        }

        $content = "<?php\nreturn ";
        $content .= var_export($form['content']->getValue(), TRUE);
        $content .= ";";

        if (!@file_put_contents('safe://' . Environment::expand('%titlePageFile%'), $content)) {
            $form->addError(__('Cannot write to file.'));
            return ;
        }

        $this->redirect('this');
        $this->terminate();
    }
}
