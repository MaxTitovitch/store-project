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
      <template v-slot:item.user_id="{ item }">
        {{ getTextByValue(item.user_id, userItems) }}
      </template>
      <template v-slot:item.top_id="{ item }">
        {{ item.top_id ? getTextByValue(item.top_id, topItems) : 'НЕТ' }}
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">CRUD опеарции: Статьи</v-toolbar-title>
          <v-divider
              class="mx-4"
              inset
              vertical
          />
          <v-spacer/>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  :disabled="selected.length < 1"
                  @click="deleteItems()"
              >Удалить
              </v-btn>
              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  v-bind="attrs"
                  v-on="on"
                  @click="createItem()"
              >Добавить
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
                  <v-form v-model="valid" ref="form" validation>
                    <v-row>

                      <v-col cols="12" sm="12" md="12">
                        <v-text-field color="#FF9765" v-model="editedItem.name" :rules="nameRules" label="Название"/>
                      </v-col>

                      <v-col cols="12" sm="12" md="12">
                        <v-textarea
                            color="#FF9765"
                            v-model="editedItem.text"
                            :rules="textRules"
                            label="Текст"
                            filled
                            auto-grow
                        />
                      </v-col>

                      <v-col cols="12" sm="12" md="12">
                        <v-switch
                            v-model="isTop"
                            :label="`Статья-топ: ${isTop ? 'Да' : 'Нет'}`"
                            color="#ff9966"
                        />
                      </v-col>


                      <v-col cols="12" sm="12" md="12" v-if="isTop">
                        <v-text-field color="#FF9765" v-model="editedItem.top.name" :rules="nameRules" label="Название топа"/>
                      </v-col>

                      <v-col cols="12" sm="12" md="12"  v-if="isTop">
                        <v-select
                            :items="productItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.top.products"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Товары (от лучшего к худшему)"
                            color="#ff9966"
                            multiple
                            :rules="selectedRules"
                        />
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
                >
                  Сохранить
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
  export default {
    data () {
      return {
        addImagesLabel: 'Добавить',
        search: '',
        menu: '',
        message: '',
        error: '',
        isTop: false,
        isTopStart: false,
        dialog: false,
        valid: false,
        selected: [],
        productItems: [],
        userItems: [],
        topItems: [],
        headers: [
          { text: 'ИД', value: 'id' },
          { text: 'Название', value: 'name' },
          { text: 'Дата публикации', value: 'date' },
          { text: 'Текст', value: 'text' },
          { text: 'Автор', value: 'user_id' },
          { text: 'Топ', value: 'top_id' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        items: [],
        editedIndex: -1,
        editedItem: {
          id: '',
          name: '',
          text: '',
          top_id: '',
        },
        editedTop: {
          name: '',
          products: [],
        },
        defaultItem: {
          id: '',
          name: '',
          text: '',
          top_id: '',
        },
        defaultTop: {
          name: '',
          products: [],
        },
        nameRules: [
          v => (v && v.length >= 10) || 'Текст должен быть длинной более 10-х символов'
        ],
        textRules: [
          v => (v && v.length >= 20) || 'Текст должен быть длинной более 20-х символов'
        ],
        selectedRules: [
          v => !!v || 'Выберите элементы!',
          v => (v && v.length >= 3)|| 'Выберите минимум 3 элемента!',
        ],
      }
    },

    created () {
      this.initialize()
    },
    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Создание статьи' : 'Редактирование статьи'
      },
      isLoading: function () { return this.$store.getters.isLoading },
      isLoggedId: function () { return this.$store.getters.isLoggedId }
    },
    watch: {
      dialog (val) {
        val || this.close()
      }
    },
    methods: {
      initialize () {
        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'posts' })
          .then((resp) => {
            this.items = resp.data
          })
          .catch(err => this.$router.push('/'))

        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'products' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.productItems.push({ text: resp.data[i].name, value: resp.data[i].id })
            }
          })
          .catch(err => this.$router.push('/'))

        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'users' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.userItems.push({ text: `${resp.data[i].name} ${resp.data[i].last_name}`, value: resp.data[i].id })
            }
          })
          .catch(err => this.$router.push('/'))

        this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'tops' })
          .then((resp) => {
            for (let i = 0; i < resp.data.length; i++) {
              this.topItems.push({ text: resp.data[i].name, value: resp.data[i].id })
            }
          })
          .catch(err => this.$router.push('/'))
      },

      showItem (id) {
        this.$router.push('/posts/' + id)
      },

      editItem (item) {
        this.editedIndex = this.items.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.isTop = this.editedItem.top_id ? true : false;
        this.isTopStart = this.isTop ? this.editedItem.top_id : -1;
        if(this.isTop) {
          this.$store.dispatch('getEntity', { data: { where: `id = ${this.editedItem.top_id}`, order: 'id asc' }, entity: 'tops' })
            .then((resp) => {
              this.editedItem.top = resp.data[0]
              for (let i = 0; i < this.editedItem.top.products.length; i++) {
                this.editedItem.top.products[i] = {value: this.editedItem.top.products[i].id, text: this.editedItem.top.products[i].name };
              }
              console.log(resp.data[0])
              this.dialog = true
            })
            .catch(err => this.$router.push('/'))
        } else {
          this.editedItem.top = {name: '', products: []}
          this.dialog = true
        }
      },

      createItem () {
        this.isTop = false;
        this.editedIndex = -1
        this.editedItem.top = {name: '', products: []}
        this.dialog = true
      },

      deleteItem (item) {
        this.$store.dispatch('deleteEntity', { entity: 'posts', id: item.id })
          .then((resp) => {
            const index = this.items.indexOf(item)
            if(item.top_id) {
              this.deleteTop(item.top_id)
              // this.$store.dispatch('deleteEntity', { entity: 'tops', id: item.top_id })
              //   .then((resp) => {})
              //   .catch(err => {this.error = 'Ошибка удаления'})
            }
            this.items.splice(index, 1)
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка удаления'
            }
          )
      },
      deleteTop (id) {
          this.$store.dispatch('deleteEntity', { entity: 'tops', id})
            .then((resp) => {})
            .catch(err => {this.error = 'Ошибка удаления'})
      },

      deleteItems () {
        for (let i = 0; i < this.selected.length; i++) {
          this.deleteItem(this.selected[i])
        }
      },

      close () {
        this.$refs.form.reset()
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        let post = {
          name: this.editedItem.name,
          text: this.editedItem.text,
          date: new Date().toISOString().substr(0, 10),
          user_id: this.isLoggedId,
        }

        if (this.editedIndex > -1) {
          if(!this.isTop) {
            this.updateEntity('posts', post, this.editedItem.id)
            if(this.isTopStart !== -1) {
              this.deleteTop(this.isTopStart)
            }
            this.close()
          } else {
            let top = {
              name: this.editedItem.top.name,
              size: this.editedItem.top.products.length
            }
            if(this.isTopStart === -1) {
              this.saveEntity('tops', top, (top) => {
                post.top_id = top.id
                this.updateEntity('posts', post, this.editedItem.id, (post) => {
                  let ids = [];
                  for (let i = 0; i < this.editedItem.top.products.length; i++) {
                    ids.push(this.editedItem.top.products[i])
                  }
                  this.updateEntity('product-top', { 'products': ids }, post.top_id, () => { this.close() })
                })
              })
            } else {
              this.updateEntity('tops', top, this.editedItem.top_id, (top) => {
                post.top_id = top.id
                this.updateEntity('posts', post, this.editedItem.id, (post) => {
                  let ids = [];
                  for (let i = 0; i < this.editedItem.top.products.length; i++) {
                    ids.push(this.editedItem.top.products[i])
                  }
                  this.updateEntity('product-top', { 'products': ids }, post.top_id, () => { this.close() })
                })
              })
            }
          }
        } else {
          if(!this.isTop) {
            this.saveEntity('posts', post)
            this.close()
          } else {
            let top = {
              name: this.editedItem.top.name,
              size: this.editedItem.top.products.length
            }
            this.saveEntity('tops', top, (top) => {
              post.top_id = top.id
              this.saveEntity('posts', post, (post) => {
                let ids = [];
                for (let i = 0; i < this.editedItem.top.products.length; i++) {
                  ids.push(this.editedItem.top.products[i])
                }
                this.updateEntity('product-top', {'products': ids}, post.top_id, () => { this.close() })
              })
            })
          }
        }

      },
      saveEntity(entity, data, fun = false) {
        this.$store.dispatch('postEntity', {entity, data})
          .then((resp) => {
            if(fun) {
              fun(resp.data)
            }
            this.initialize()
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка добавления'
            }
          )
      },
      updateEntity(entity, data, id, fun = false) {
        console.log({entity, data, id})
        this.$store.dispatch('putEntity', {entity, data, id})
          .then((resp) => {
            if(fun) {
              fun(resp.data)
            }
            this.initialize()
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка добавления'
            }
          )
      },
      getTextByValue (value, items, defaultValue = false) {
        for (let i = 0; i < items.length; i++) {
          if (items[i].value === value) {
            return defaultValue ? defaultValue : items[i].text
          }
        }
        return value
      }
    }
  }
</script>

<style>

</style>