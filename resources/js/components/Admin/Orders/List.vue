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
        {{ getTextByValue(item.user_id, userItems) || '-' }}
      </template>
      <template v-slot:item.address_id="{ item }">
        {{ getTextByValue(item.address_id, addressItems) || '-' }}
      </template>
      <template v-slot:item.comment="{ item }">
        {{ item.comment || '-' }}
      </template>
      <template v-slot:item.products="{ item }">
        {{ getProducts(item.products) }}
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">CRUD опеарции: Заказы</v-toolbar-title>
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
                  <v-form v-model="valid" ref="form" validation>
                    <v-row>
                      <v-col cols="12" sm="6" md="4">
                        <v-menu
                            ref="menuStart"
                            v-model="menuStart"
                            :close-on-content-click="false"
                            transition="scale-transition"
                            offset-y
                            min-width="290px"
                            color="#ff9966"
                        >
                          <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="editedItem.date"
                                label="Дата заказа"
                                readonly
                                v-on="on"
                                :rules="dateRules"
                                color="#ff9966"
                            />
                          </template>
                          <v-date-picker
                              v-model="editedItem.date"
                              scrollable
                              locale="ru-ru"
                              :min="'1900-01-01'"
                              :max="getNextYear()"
                              first-day-of-week="1"
                              header-color="#ff9765"
                          >
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="menuStart = false">Отмена</v-btn>
                            <v-btn text color="primary" @click="$refs.menuStart.save(editedItem.date)">ОК</v-btn>
                          </v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-menu
                            ref="menuEnd"
                            v-model="menuEnd"
                            :close-on-content-click="false"
                            transition="scale-transition"
                            offset-y
                            min-width="290px"
                            color="#ff9966"
                        >
                          <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="editedItem.delivery_date"
                                label="Дата доставки"
                                readonly
                                v-on="on"
                                :rules="deliveryDateRules"
                                color="#ff9966"
                            />
                          </template>
                          <v-date-picker
                              v-model="editedItem.delivery_date"
                              scrollable
                              locale="ru-ru"
                              :min="editedItem.date"
                              :max="getNextYear()"
                              first-day-of-week="1"
                              header-color="#ff9765"
                          >
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="menuEnd = false">Отмена</v-btn>
                            <v-btn text color="primary" @click="$refs.menuEnd.save(editedItem.delivery_date)">ОК</v-btn>
                          </v-date-picker>
                        </v-menu>
                      </v-col>

                      <v-col cols="12" sm="6" md="4">
                        <v-select
                            :items="statusItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.status"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Статус"
                            color="#ff9966"
                            :rules="selectedRules"
                        />
                      </v-col>

                      <v-col cols="12" sm="12" md="12">
                        <v-textarea
                            color="#FF9765"
                            v-model="editedItem.comment"
                            :rules="commentRules"
                            label="Комментарий к заказу"
                            filled
                            auto-grow
                        />
                      </v-col>

                      <v-col cols="12" sm="6" md="6">
                        <v-select
                            :items="userItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.user_id"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Пользователь"
                            color="#ff9966"
                            clearable
                            @change="changeUser()"
                        />
                      </v-col>

                      <v-col cols="12" sm="6" md="6">
                        <v-select
                            :items="addressItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.address_id"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Адрес"
                            clearable
                            :disabled="editedItem.user_id == ''"
                            color="#ff9966"
                        />
                      </v-col>

                      <v-col cols="12" sm="12" md="12" >
                        <v-select
                            :items="productItems"
                            item-text="text"
                            item-value="value"
                            v-model="editedItem.products"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Товары"
                            color="#ff9966"
                            multiple
                            chips
                            :rules="selectedManyRules"
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
      search: '',
      message: '',
      error: '',
      dialog: false,
      valid: false,
      menuStart: false,
      menuEnd: false,
      selected: [],
      statusItems: [{value: 'В обработке', text: 'В обработке' }, {value: 'Выполнен', text: 'Выполнен' }, {value: 'Отменён', text: 'Отменён' }],
      userItems: [],
      addressItems: [],
      productItems: [],
      headers: [
        { text: 'ИД', value: 'id' },
        { text: 'Дата заказа', value: 'date' },
        { text: 'Дата доставки', value: 'delivery_date' },
        { text: 'Статус', value: 'status' },
        { text: 'Комментарий', value: 'comment' },
        { text: 'Пользователь', value: 'user_id' },
        { text: 'Адрес', value: 'address_id' },
        { text: 'Товары', value: 'products' },
        { text: 'Действия', value: 'actions', sortable: false },
      ],
      items: [],
      editedIndex: -1,
      editedItem: {
        date: '',
        delivery_date: '',
        status: '',
        comment: '',
        user_id: '',
        address_id: '',
        products: '',
      },
      defaultItem: {
        date: '',
        delivery_date: '',
        status: '',
        comment: '',
        user_id: '',
        address_id: '',
        products: '',
      },
      commentRules: [
        v => (!!this.editedItem.address_id ? true : !!v ) || 'Выведите коммент!',
        v => (!v || v.length >= 10) || 'Описание должно быть длинной более 10-х символов'
      ],
      selectedManyRules: [
        v => !!v || 'Выберите элементы!',
        v => (v && v.length >= 1)|| 'Выберите минимум 1 товар!',
      ],
      selectedRules: [
        v => !!v || 'Выберите элементы!',
      ],
      dateRules: [
        v => !!v || 'Введите дату!'
      ],
      deliveryDateRules: [
        v => !!v || 'Введите дату!',
        v => new Date(v) >= new Date(this.editedItem.date) || 'Введите после даты заказа!'
      ],
    }
  },

  created () {
    this.initialize()
  },
  computed: {
    formTitle () {
      return this.editedIndex === -1 ? 'Создание заказа' : 'Редактирование заказа'
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
      this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'orders' })
        .then((resp) => {
          this.items = resp.data
          for (let i = 0; i < this.items.length; i++) {
            for (let j = 0; j < this.items[i].products.length; j++) {
              this.items[i].products[j] = { text: this.items[i].products[j].name, value: this.items[i].products[j].id };
            }
          }
        })
        .catch(err => this.$router.push('/'))

      this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'addresses' })
        .then((resp) => {
          for (let i = 0; i < resp.data.length; i++) {
            this.addressItems.push({ text: `${resp.data[i].city}, ул.${resp.data[i].street} ${resp.data[i].house}`, value: resp.data[i].id })
          }
        })
        .catch(err => { this.$router.push('/')})

      this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'users' })
        .then((resp) => {
          for (let i = 0; i < resp.data.length; i++) {
            this.userItems.push({ text: `${resp.data[i].name} ${resp.data[i].last_name}`, value: resp.data[i].id })
          }
        })
        .catch(err => { this.$router.push('/')})

      this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'products' })
        .then((resp) => {
          for (let i = 0; i < resp.data.length; i++) {
            this.productItems.push({ text: resp.data[i].name, value: resp.data[i].id })
          }
        })
        .catch(err => { this.$router.push('/')})
    },

    editItem (item) {
      this.editedIndex = this.items.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.getAddresses(this.editedItem.user_id)
      this.dialog = true
    },
    createItem () {
      this.editedIndex = -1
      this.dialog = true
    },

    deleteItem (item) {
      this.$store.dispatch('deleteEntity', { entity: 'orders', id: item.id })
        .then((resp) => {
          const index = this.items.indexOf(item)
          this.items.splice(index, 1)
        })
        .catch(err => {
            this.initialize()
            this.error = 'Ошибка удаления'
          }
        )
    },

    showBarChart () {
      this.$router.push('/admin/schedule/pie?param=sex')
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
      let order = {
        date: this.editedItem.date,
        delivery_date: this.editedItem.delivery_date,
        status: this.editedItem.status,
        comment: this.editedItem.comment,
        address_id: this.editedItem.address_id,
        user_id: this.editedItem.user_id,
      }, ids = [];
      for (let i = 0; i < this.editedItem.products.length; i++) {
        ids.push(this.editedItem.products[i].value || this.editedItem.products[i])
      }
      if (this.editedIndex > -1) {
        this.$store.dispatch('putEntity', { data: order, id: this.editedItem.id, entity: 'orders' })
          .then((resp) => {
            this.$store.dispatch('putEntity', {entity: 'order-product', data: { 'products': ids }, id: resp.data.id})
              .then((resp) => {this.initialize()})
              .catch(err => { this.initialize(); this.error = 'Ошибка добавления'})
            this.initialize()
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка обновления'
            }
          )
      } else {
        this.$store.dispatch('postEntity', { entity: 'orders', data: order })
          .then((resp) => {
            this.$store.dispatch('putEntity', {entity: 'order-product', data: { 'products': ids }, id: resp.data.id})
              .then((resp) => {this.initialize()})
              .catch(err => { this.initialize(); this.error = 'Ошибка добавления'})
            this.initialize()
          })
          .catch(err => {
              this.initialize()
              this.error = 'Ошибка добавления'
            }
          )
      }
      this.close()
    },
    getTextByValue (value, items) {
      for (let i = 0; i < items.length; i++) {
        if (items[i].value === value) {
          return items[i].text
        }
      }
      return value
    },
    getProducts (products) {
      let stringProducts = '';
      for (let i = 0; i < products.length; i++) {
        stringProducts += products[i].text;
        if(i < products.length -1) stringProducts += ', ';
      }
      return stringProducts;
    },
    getNextYear () {
      let date = new Date()
      date.setFullYear(date.getFullYear() + 1)
      return date.toISOString().substr(0, 10)
    },
    changeUser () {
      if(this.editedItem.user_id != null) {
        this.getAddresses(this.editedItem.user_id)
      } else this.addressItems = [];
    },
    getAddresses(id) {
      this.addressItems = [];
      this.$store.dispatch('getEntity', { data: {where: `user_id = ${id}`, order: 'id asc' }, entity: 'addresses' })
        .then((resp) => {
          for (let i = 0; i < resp.data.length; i++) {
            this.addressItems.push({ text: `${resp.data[i].city}, ул.${resp.data[i].street} ${resp.data[i].house}`, value: resp.data[i].id })
          }
        })
        .catch(err => { this.$router.push('/')})
    }
  }
}
</script>

<style>

</style>