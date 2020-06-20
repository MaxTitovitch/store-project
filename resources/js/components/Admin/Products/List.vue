<template>
  <div>
    <v-data-table
        v-model="selected"
        :headers="headers"
        :items="items"
        item-key="id"
        show-select
        class="elevation-1 mt-5"
        :search="search"
        :loading="isLoading"
        color="#FF9765"
        loading-text="Загрузка... Пожалуйста, подаждите"
        :footerProps="{
          itemsPerPageText: 'Элементов на странице',
          'items-per-page-options': [5, 10, 20, {text: 'Всё', value: -1}],
        }"
    >
      <template v-slot:item.category_id="{ item }">
        {{ getTextByValue(item.category_id, categoryItems) || '-' }}
      </template>
      <template v-slot:item.tags="{ item }">
        {{ getTags(item.tags) || '-'}}
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">CRUD опеарции: Товары</v-toolbar-title>
          <v-divider
              class="mx-4"
              inset
              vertical
          />
          <v-spacer/>
          <v-dialog v-model="dialog" max-width="1000px">
            <template v-slot:activator="{ on, attrs }">

              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  @click="showBarChart()"
              >
                Статистика
              </v-btn>
              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  :disabled="selected.length < 1"
                  @click="deleteItems()"
              >
                Удалить
              </v-btn>
              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  v-bind="attrs"
                  v-on="on"
                  @click="createItem()"
              >
                Добавить
              </v-btn>
              <v-text-field
                  v-model="search"
                  append-icon="mdi-magnify"
                  label="ПОИСК..."

                  hide-details
                  dark
                  style="max-width: 20%"
                  class="mx-2"
              />
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <div class="file-input-area">
                    <ImagePicker
                        v-model="images"
                        :max="5"
                        :min="1"
                        :activeImageUploads="activeImageUploads"
                        exceedMaxImagesError="Вы выбрали слишком много картинок"
                        invalidFileTypeError="Загрузите картинку типа: png, jpg или gif"
                        maxImagesUnit="Изображений"
                        clearedImagesMessage="Очищено"
                        clearImagesLabel="Очистить"
                        :addImagesLabel="addImagesLabel"
                        justify-content
                    >
                      <v-flex xs4 md3>
                        <div class="v-card v-sheet theme--light">
                          <div class="v-responsive v-image">
                            <div class="v-responsive__sizer" style="padding-bottom: 100%;"></div>
                            <img class="v-image__image v-image__image--contain"
                                :src="`${placeholderImage}-1.png?d=${new Date()}`"
                                 onerror="if (this.src != '/images/empty-product.jpg') this.src = '/images/empty-product.jpg';"
                                 width="100%" height="100%">
                            <div class="v-responsive__content"></div>
                          </div>
                        </div>


                      </v-flex>
                      <v-flex xs4 md3 v-for="item of arrayIds" :key="item">
                        <div class="v-card v-sheet theme--light">
                          <div class="v-responsive v-image">
                            <div class="v-responsive__sizer" style="padding-bottom: 100%;"></div>
                            <img class="v-image__image v-image__image--contain"
                                :src="`${placeholderImage}-${item}.png?d=${new Date()}`"
                                onerror="this.parentElement.parentElement.parentElement.setAttribute('style', 'display: none')"
                                onload="this.parentElement.parentElement.parentElement.setAttribute('style', 'display: block')"
                                width="100%"
                                height="100%">
                            <div class="v-responsive__content"></div>
                          </div>
                        </div>

                      </v-flex>
                    </ImagePicker>
                  </div>
                  <v-form v-model="valid" ref="form" validation>
                    <v-row>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.name"
                            :rules="nameRules"
                            label="Название"
                        />
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.price"
                            :rules="priceRules"
                            label="Цена"
                        />
                      </v-col>

                      <v-col cols="12" sm="6" md="4">
                        <v-select
                            :items="categoryItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.category_id"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Категория"
                            color="#ff9966"
                            :rules="selectedRules"
                        />
                      </v-col>

                      <v-col cols="12" sm="12" md="12">
                        <v-textarea
                            color="#FF9765"
                            v-model="editedItem.description"
                            :rules="descRules"
                            label="Описание"
                            filled
                            auto-grow
                        />
                      </v-col>

                      <v-col cols="12" sm="12" md="12">
                        <v-select
                            :items="tagItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.tags"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Теги"
                            color="#ff9966"
                            multiple
                            chips
                        />
                      </v-col>

                      <v-col cols="12" sm="12" md="12">
                        <v-btn
                            dark class="gradient-project" style="width: 100%" text
                            @click="showCharacteristics(editedItem.id, editedItem.name)"
                            :disabled="editedIndex == -1"
                        >
                          Характеристики
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-form>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer/>
                <v-btn dark class="gradient-project" text @click="close">Отмена</v-btn>
                <v-btn dark class="gradient-project" text
                       @click="save"
                       :disabled="!valid"
                       v-bind:dark="valid"
                >Сохранить
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon
            small
            class="mr-2"
            @click="showCharacteristics(item.id, item.name)"
        >
          mdi-format-list-bulleted-square
        </v-icon>
        <v-icon
            small
            class="mr-2"
            @click="showItem(item.id)"
        >
          mdi-eye
        </v-icon>
        <v-icon
            small
            class="mr-2"
            @click="editItem(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
            small
            @click="deleteItem(item)"
        >
          mdi-delete
        </v-icon>
      </template>
      <template v-slot:no-data>
        <v-btn dark class="gradient-project" @click="initialize">Повторить загрузку</v-btn>
      </template>
    </v-data-table>
    <v-alert type="success" v-if="message">
      {{ message }}
    </v-alert>
    <v-alert type="error" v-if="error">
      {{ error }}
    </v-alert>
  </div>

</template>

<script>
  import { ImagePicker, imageUploadingStates } from '@nagoos/vue-image-picker'

  export default {
    components: {
      ImagePicker
    },
    data () {
      return {
        addImagesLabel: 'Добавить',
        search: '',
        message: '',
        error: '',
        dialog: false,
        valid: false,
        menuStart: false,
        menuEnd: false,
        arrayIds: [],
        images: [],
        activeImageUploads: {},
        placeholderImage: '',
        selected: [],
        categoryItems: [],
        tagItems: [],
        headers: [
          { text: 'ИД', value: 'id' },
          { text: 'Название', value: 'name' },
          { text: 'Цена', value: 'price' },
          { text: 'Описание', value: 'description' },
          { text: 'Рейтинг', value: 'ranking' },
          { text: 'Категория', value: 'category_id' },
          { text: 'Теги', value: 'tags' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        items: [],
        editedIndex: -1,
        editedItem: {
          name: '',
          price: '',
          description: '',
          ranking: '',
          category_id: '',
          tags: '',
        },
        defaultItem: {
          name: '',
          price: '',
          description: '',
          ranking: '',
          category_id: '',
          tags: '',
        },
        selectedRules: [
          v => !!v || 'Выберите элементы!',
        ],
        nameRules: [
          v => !!v || 'Введите назвние!',
          v => (v && v.length >= 3) || 'Текст должен быть длинной более 3-х символов',
          v => (v && v.length <= 100) || 'Текст должен быть длинной менее 100-х символов'
        ],
        priceRules: [
          v => !!v || 'Введите цену!',
          v => (v && !isNaN(v) && v > 0 && v <= 1000) || 'Введите число от 0 до 1000!',
        ],
        descRules: [
          v => !!v || 'Введите описание!',
          v => (v && v.length >= 20) || 'Описание должно быть длинной более 20-х символов'
        ],
      }
    },

    created () {
      this.initialize()
    },
    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Создание товара' : 'Редактирование товара'
      },
      isLoading: function () { return this.$store.getters.isLoading }
    },
    watch: {
      dialog (val) {
        val || this.close()
      },
    },
    methods: {
      initialize () {
        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'products' })
          .then((resp) => {
            this.items = resp.data
            for (let i = 0; i < this.items.length; i++) {
              for (let j = 0; j < this.items[i].tags.length; j++) {
                this.items[i].tags[j] = { text: this.items[i].tags[j].name, value: this.items[i].tags[j].id }
              }
            }
          })
          .catch(err => this.$router.push('/'))

        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'categories' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.categoryItems.push({ text: resp.data[i].name, value: resp.data[i].id })
            }
          })
          .catch(err => { this.$router.push('/')})

        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'tags' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.tagItems.push({ text: resp.data[i].name, value: resp.data[i].id })
            }
          })
          .catch(err => { this.$router.push('/')})
      },

      showItem (id) {
        this.$router.push('/products/' + id)
      },

      showCharacteristics (id, name) {
        this.$router.push('/admin/product-characteristics?id=' + id + '&name=' + name)
      },

      editItem (item) {
        this.arrayIds = [2, 3, 4, 5]
        try {
          this.placeholderImage = `/storage/products/${item.slug}`
          this.addImagesLabel = 'Сменить'
        } catch (e) { }
        this.editedIndex = this.items.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
        setTimeout(() => {document.querySelectorAll('.v-btn--depressed')[0].click()}, 100)

      },
      createItem () {
        this.editedIndex = -1
        this.dialog = true
      },

      deleteItem (item) {
        this.$store.dispatch('deleteEntity', { entity: 'products', id: item.id })
          .then((resp) => {
            const index = this.items.indexOf(item)
            this.items.splice(index, 1)
            for (let i = 1; i <= 5; i++) {
              this.deletePhoto(resp.data.slug + '-' + (i))
            }
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка удаления'
            }
          )
      },

      deletePhoto (slug) {
        this.$store.dispatch('postEntity', { entity: 'delete-photo', data: { type: 'products', slug } })
          .then((resp) => {}).catch(err => {})
      },

      deleteItems () {
        for (let i = 0; i < this.selected.length; i++) {
          this.deleteItem(this.selected[i])
        }
      },

      close (fromSave = false) {
        this.$refs.form.reset()
        this.addImagesLabel = 'Добавить'
        this.placeholderImage = ''

        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        let product = {
          name: this.editedItem.name,
          price: this.editedItem.price,
          description: this.editedItem.description,
          ranking: this.editedItem.ranking || 5,
          category_id: this.editedItem.category_id,
        }, ids = []
        for (let i = 0; i < this.editedItem.tags.length; i++) {
          ids.push(this.editedItem.tags[i].value || this.editedItem.tags[i])
        }
        if (this.editedIndex > -1) {
          this.$store.dispatch('putEntity', { data: product, id: this.editedItem.id, entity: 'products' })
            .then((resp) => {
              this.createPhoto(resp.data)
              this.$store.dispatch('putEntity', { entity: 'product-tag', data: { 'tags': ids }, id: resp.data.id })
                .then((resp) => {this.initialize()})
                .catch(err => {
                  this.initialize()
                  this.error = 'Ошибка добавления'
                })
              this.initialize()
            })
            .catch(err => {
                this.initialize()
                this.error = 'Ошибка обновления'
              }
            )
        } else {
          this.$store.dispatch('postEntity', { entity: 'products', data: product })
            .then((resp) => {
              this.createPhoto(resp.data)
              this.$store.dispatch('putEntity', { entity: 'product-tag', data: { 'tags': ids }, id: resp.data.id })
                .then((resp) => {this.initialize()})
                .catch(err => {
                  this.initialize()
                  this.error = 'Ошибка добавления'
                })
              this.initialize()
            })
            .catch(err => {
                this.initialize()
                this.error = 'Ошибка добавления'
              }
            )
        }
        this.close(true)
      },
      getTextByValue (value, items) {
        for (let i = 0; i < items.length; i++) {
          if (items[i].value === value) {
            return items[i].text
          }
        }
        return value
      },
      getTags (tags) {
        let stringTags = ''
        for (let i = 0; i < tags.length; i++) {
          stringTags += tags[i].text
          if (i < tags.length - 1) stringTags += ', '
        }
        return stringTags
      },
      createPhoto (product) {
        if (this.images.length > 0) {
          for (let i = 1; i <= 5; i++) {
            if (i <= this.images.length)
              this.$store.dispatch('uploadPhoto', {
                type: 'products',
                file: this.images[i - 1],
                slug: product.slug + '-' + (i)
              })
                .then((resp) => {}).catch(err => {})
            else
              this.deletePhoto(product.slug + '-' + (i))
          }
        }
      },
      showBarChart () {
        this.$router.push('/admin/schedule/bar?entity=product&param=views')
      },
    }
  }
</script>

<style>
  .file-input {
    display: none !important;
  }

  .file-input-area i {
    display: none !important;
  }

  .file-input-area .layout {
    display: flex;
    justify-content: center;
  }

  .file-input-area .layout div {
    text-align: center;
  }

  .v-snack__content {
    background: linear-gradient(180deg, #FF5F66, #FF9765);
  }

  .v-snack {
    opacity: 0;
  }

  .file-input-area .layout.wrap {
     flex-wrap: nowrap;
  }

</style>