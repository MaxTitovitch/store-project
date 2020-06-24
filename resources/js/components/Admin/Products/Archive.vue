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
          <v-toolbar-title color="white">Архив: Товары</v-toolbar-title>
          <v-divider
              class="mx-4"
              inset
              vertical
          />
          <v-spacer/>
          <v-dialog max-width="1000px">
            <template v-slot:activator="{ on, attrs }">

              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  :disabled="selected.length < 1"
                  @click="restoreItems()"
              >
                Востановить
              </v-btn>
              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  @click="showProducts()"
              >
                Назад к Товарам
              </v-btn>
            </template>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon
            small
            @click="restoreItem(item)"
        >
          mdi-file-restore
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
        selected: [],
        categoryItems: [],
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
      }
    },
    created () {
      this.initialize()
    },
    computed: {
      isLoading: function () { return this.$store.getters.isLoading }
    },
    methods: {
      initialize () {
        this.$store.dispatch('getEntity', { data: { trash: 'true', order: 'id asc' }, entity: 'products' })
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
      },

      restoreItem (item) {
        let product = {
          name: item.name,
          price: item.price,
          description: item.description,
          ranking: item.ranking,
          category_id: item.category_id,
          deleted_at: null,
        }
        this.$store.dispatch('putEntity', { entity: 'products', id: item.id, data: product })
          .then((resp) => {
            const index = this.items.indexOf(item)
            this.items.splice(index, 1);
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка удаления'
            }
          )
      },


      restoreItems () {
        for (let i = 0; i < this.selected.length; i++) {
          this.restoreItem(this.selected[i])
        }
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
      showProducts () {
        this.$router.push('/admin/products')
      },
    }
  }
</script>

<style>
</style>