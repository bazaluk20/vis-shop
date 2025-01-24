<link rel="stylesheet" href="{$rootUrl}/Okay/Modules/SimplaMarket/ProductGallery/Backend/design/css/product_gallery.css">
<script src="{$rootUrl}/Okay/Modules/SimplaMarket/ProductGallery/Backend/design/js/vue.js"></script>

<button class="btn btn_blue file-manager-btn fn_gallery_file_manager" type="button">{$btr->sm__product_gallery__open_file_manager}</button>

{literal}
<div id="fn_file_manager" class="product-file-manager" style="display: none;" data-product_id="{/literal}{$product->id}{literal}">
    <div class="product-file-manager__head">

        <div class="file-manager-informer">
            <span class="file-manager-informer__title">{/literal}{$btr->sm__product_gallery__file_manager_title}{literal}</span>
            <button class="btn btn_blue file-manager-informer__back" v-if="currentDirId !== 0" v-on:click="leftDirectory" type="button">{/literal}{$btr->sm__product_gallery__back}{literal}</button>
        </div>

        <div class="tab-product-gallery">
            <div class="tab-product-gallery__head">
                <button class="btn" v-bind:class="{'btn_blue': activeDownloadImageTab}" v-on:click="setActiveTab('file-download')">{/literal}{$btr->sm__product_gallery__upload_image}{literal}</button>
                <button class="btn" v-bind:class="{'btn_blue': activeCreateDirTab}"     v-on:click="setActiveTab('directory-create')">{/literal}{$btr->sm__product_gallery__create_dir}{literal}</button>
                <button class="btn" v-bind:class="{'btn_blue': activeDropModeTab}"      v-on:click="setActiveTab('drop-mode')">{/literal}{$btr->sm__product_gallery__deleting_mode}{literal}</button>
            </div>

            <div class="tab-product-gallery__body" v-if="activeTab === 'file-download'">
                <hr>
                <span class="directory-editor__label">{/literal}{$btr->sm__product_gallery__upload_image}{literal}: </span>
                <input class="file-downloader__file" type="file" name="gallery_files[]" multiple />
                <button class="file-downloader__button btn btn_small btn_blue" v-on:click="uploadFiles" type="button">{/literal}{$btr->sm__product_gallery__upload}{literal}</button>
            </div>

            <div class="tab-container" v-if="activeTab === 'directory-create'">
                <hr>
                <div>
                    <span class="directory-editor__label">{/literal}{$btr->sm__product_gallery__create_dir}{literal}: </span>
                    <input class="directory-editor__input" v-model="newDirName" />
                    <button class="directory-editor__button btn btn_small btn_blue" v-on:click="createDirectory">{/literal}{$btr->sm__product_gallery__create}{literal}</button>
                </div>
            </div>

            <hr>

        </div>
    </div>
    <div class="product-file-manager__body files-container">
        <div class="single-file" v-for="image in images">
            <div class="single-file__wrapper">
                <div class="single-file__folder-content" v-on:click="visitDirectory(image.id)" v-if="image.is_dir === true">
                    <div class="single-file__folder"><img class="single-file__folder-image" src="/Okay/Modules/SimplaMarket/ProductGallery/Backend/design/images/directory.png" /></div>
                    <div class="single-file__folder-name">{{ image.name }}</div>
                </div>
                <div class="single-file__img-content" v-if="image.is_dir === false" v-on:click="attachImageToProduct(image.id)">
                    <div class="single-file__img-container"><img :src="image.src" class="single-file__img" /></div>
                    <div v-if="image.in_product === true" class="single-file__in-product-label">{/literal}{$btr->sm__product_gallery__in_product}{literal}</div>
                </div>
                <span class="single-file__delete" v-if="activeDropModeTab" v-on:click="deleteItem(image.id, image.is_dir)">X</span>
            </div>
        </div>
    </div>
</div>

<script>
    $('.fn_gallery_file_manager').on('click', function() {
        $.fancybox.open({src: '#fn_file_manager'});
    });

    new Vue({
        el: '#fn_file_manager',
        data: {
            currentDirId: 0,
            parentDirId:  0,
            images:       [],
            newDirName:   '',

            activeTab: ''
        },
        computed: {
            activeDownloadImageTab: function() {
                if (this.activeTab === 'file-download') {
                    return true;
                }
                return false;
            },
            activeCreateDirTab: function() {
                if (this.activeTab === 'directory-create') {
                    return true;
                }
                return false;
            },
            activeDropModeTab: function() {
                if (this.activeTab === 'drop-mode') {
                    return true;
                }
                return false;
            },
        },
        mounted: function() {
            getDirectoryData().done(response => {
                if (response.ok) {
                    this.images = response.data.images;
                }
            });
        },
        methods: {
            setActiveTab: function(tabLabel) {
                if (this.activeTab === tabLabel) {
                    this.activeTab = '';
                    return;
                }
                this.activeTab = tabLabel;
            },
            visitDirectory: function(directoryId) {
                this.parentDirId  = this.currentDirId;
                this.currentDirId = directoryId;
                getDirectoryData(directoryId).done(response => {
                    if (response.ok) {
                        this.images = response.data.images;
                    }
                });
            },
            leftDirectory: function() {
                getDirectoryData(this.parentDirId).done(response => {
                    if (response.ok) {
                        this.currentDirId = this.parentDirId;
                        this.parentDirId  = response.data.parent_directory_id;
                        this.images       = response.data.images;
                    }
                });
            },
            createDirectory: async function() {
                if (! this.newDirName) {
                    return;
                }

                await createDirectory(this.currentDirId, this.newDirName);
                getDirectoryData(this.currentDirId).done(response => {
                    if (response.ok) {
                        this.images     = response.data.images;
                        this.newDirName = '';
                    }
                });
            },
            attachImageToProduct: async function(id) {
                const images = this.images.map(img => {
                    if (!img.is_dir && img.id === id) {
                        if (img.in_product === true) {
                            img.in_product = false;
                            detachImageToProductId(id)
                        } else {
                            img.in_product = true;
                            attachImageToProductId(id)
                        }
                    }
                    return img;
                });
                this.images = images;
            },
            uploadFiles: async function() {
                await uploadImageToDirectory(this.currentDirId);
                getDirectoryData(this.currentDirId).done(response => {
                    if (response.ok) {
                        this.images = response.data.images;
                    }
                });
            },
            deleteItem: async function(id, isDir) {
                if (isDir) {
                    await deleteDirectory(id);
                } else {
                    await dropFile(id);
                }
                getDirectoryData(this.currentDirId).done(response => {
                    if (response.ok) {
                        this.images = response.data.images;
                    }
                });
            }
        },
    });

    async function createDirectory(parentDirectoryId, name) {
        return $.ajax({
            url: '{/literal}{$rootUrl}{literal}/api/sm/products-gallery/directory/create',
            type: 'POST',
            dataType: 'json',
            data: {
                parentDirectoryId,
                name
            }
        });
    }

    async function dropFile(id) {
        return $.ajax({
            url: `{/literal}{$rootUrl}{literal}/api/sm/products-gallery/image/delete/${id}`,
            type: 'POST',
            dataType: 'json',
        });
    }

    async function deleteDirectory(id) {
        return $.ajax({
            url: `{/literal}{$rootUrl}{literal}/api/sm/products-gallery/directory/delete/${id}`,
            type: 'POST',
            dataType: 'json',
        });
    }

    function getDirectoryData(directoryId) {
        if (! directoryId) {
            directoryId = 0;
        }

        return $.ajax({
            url: `{/literal}{$rootUrl}{literal}/api/sm/products-gallery/directory/${directoryId}`,
            type: 'GET',
            dataType: 'json',
            data: {
                product_id: getProductId()
            }
        });
    }

    async function uploadImageToDirectory(directoryId) {
        const images = document.querySelector('[name="gallery_files[]"]').files;

        const data = new FormData();
        $.each(images, function(key, value){
            data.append(key, value);
        });

        document.querySelector('[name="gallery_files[]"]').value = '';
        return $.ajax({
            url: `{/literal}{$rootUrl}{literal}/api/sm/products-gallery/image/upload/${directoryId}`,
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data
        })
    }

    async function attachImageToProductId(imageId) {
        return $.ajax({
            url: '{/literal}{$rootUrl}{literal}/api/sm/products-gallery/image/attach',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: getProductId(),
                image_id:   imageId
            }
        })
    }

    async function detachImageToProductId(imageId) {
        return $.ajax({
            url: '{/literal}{$rootUrl}{literal}/api/sm/products-gallery/image/detach',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: getProductId(),
                image_id:   imageId
            }
        })
    }

    function getProductId() {
        if (! this.productId) {
            this.productId = $('#fn_file_manager').data('product_id');
        }
        return this.productId;
    }

</script>
{/literal}