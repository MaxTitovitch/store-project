<template>
  <div>
    <User :userId="user_id"></User>

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
          'items-per-page-options': [{text: 'Всё', value: -1}],
        }"
    >
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">Личный кабинет: Адреса</v-toolbar-title>
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
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.country"
                            :rules="textRules"
                            label="Страна"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.city"
                            :rules="textRules"
                            label="Город"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.street"
                            :rules="textRules"
                            label="Улица"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.house"
                            :rules="textRules"
                            label="Дом"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.perch"
                            :rules="numberRules"
                            label="Подъезд"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.floor"
                            :rules="numberRules"
                            label="Этаж"/>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            color="#FF9765"
                            v-model="editedItem.apartment"
                            :rules="numberRules"
                            label="Квартира"/>
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

    <v-data-table
        v-model="selectedOrder"
        :headers="headersOrder"
        :items="itemsOrder"
        item-key="id"
        show-select
        class="elevation-1 mt-5"
        :search="searchOrder"
        :loading="isLoading"
        color="#FF9765"
        loading-text="Загрузка... Пожалуйста, подаждите"
        :footerProps="{
          itemsPerPageText: 'Элементов на странице',
          'items-per-page-options': [{text: 'Всё', value: -1}],
        }"
    >
      <template v-slot:item.comment="{ item }">
        {{ item.comment || '-' }}
      </template>
      <template v-slot:item.address_id="{ item }">
        {{ getTextByValueOrder(item.address_id, items) || '-' }}
      </template>
      <template v-slot:item.user_id="{ item }">
        {{ user.name + ' ' + user.last_name }}
      </template>
      <template v-slot:item.products="{ item }">
        {{ getProducts(item.products) }}
      </template>
      <template v-slot:top>
        <v-toolbar flat dark color="#FF9765">
          <v-toolbar-title color="white">Личный кабинет: Заказы</v-toolbar-title>
          <v-divider
              class="mx-4"
              inset
              vertical
          />
          <v-spacer/>
          <v-dialog v-model="dialogOrder" max-width="1000px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                  color="#FF5F66"
                  dark
                  class="gradient-project mx-2"
                  :disabled="selectedOrder.length < 1"
                  @click="deleteOrderItems()"
              >
                Отменить
              </v-btn>
              <v-text-field
                  v-model="searchOrder"
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
                <span class="headline">{{ formTitleOrder }}</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-form ref="formOrder" validation>
                    <v-row>
                      <v-col cols="12" sm="6" md="4">
                          <v-text-field
                              v-model="editedItemOrder.date"
                              label="Дата заказа"
                              readonly
                              :disabled="true"
                              color="#ff9966"
                          />
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                          <v-text-field
                              v-model="editedItemOrder.delivery_date"
                              label="Дата доставки"
                              readonly
                              :disabled="true"
                              color="#ff9966"
                          />
                      </v-col>

                      <v-col cols="12" sm="6" md="4">
                        <v-text-field
                            v-model="editedItemOrder.status"
                            label="Статус"
                            readonly
                            :disabled="true"
                            color="#ff9966"
                        />
                      </v-col>

                      <v-col cols="12" sm="12" md="12">
                        <v-textarea
                            color="#FF9765"
                            v-model="editedItemOrder.comment"
                            label="Комментарий к заказу"
                            filled
                            auto-grow
                            :disabled="true"
                        />
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field
                            v-model="editedItemOrder.user_id"
                            label="Пользователь"
                            readonly
                            :disabled="true"
                            color="#ff9966"
                        />
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field
                            v-model="editedItemOrder.address_id"
                            label="Адрес"
                            readonly
                            :disabled="true"
                            color="#ff9966"
                        />
                      </v-col>

                      <v-col cols="12" sm="12" md="12" >
                        <v-select
                            :items="editedItemOrder.products"
                            item-text="name"
                            item-value="id"
                            v-model="editedItemOrder.products"
                            item-color="#ff9966"
                            aria-required="true"
                            label="Товары"
                            color="#ff9966"
                            multiple
                            chips
                            :disabled="true"
                        />
                      </v-col>
                    </v-row>
                  </v-form>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer/>
                <v-btn dark class="gradient-project" text @click="closeOrder">Закрыть</v-btn>
                <v-btn dark class="gradient-project" text
                       @click="deleteOrderItem(editedItemOrder)"
                       :disabled="editedItemOrder.status !== 'В обработке'"
                >
                  Отменить
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon
            small
            @click="editItemOrder(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
            small
            @click="deleteOrderItem(item)"
            title="Отменить заказ"
            v-if="item.status === 'В обработке'"
        >
          mdi-delete
        </v-icon>
      </template>
      <template v-slot:no-data>
        <v-btn dark class="gradient-project" @click="initializeOrder">Повторить загрузку</v-btn>
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
  import User from '../Pages/Users/User'
  export default {
    components: {
      User
    },
    data () {
      return {
        search: '',
        menu: '',
        message: '',
        error: '',
        dialog: false,
        valid: false,
        selected: [],
        searchOrder: '',
        dialogOrder: false,
        validOrder: false,
        selectedOrder: [],
        user: {},
        productItems: [],
        headers: [
          { text: 'ИД', value: 'id' },
          { text: 'Страна', value: 'country' },
          { text: 'Город', value: 'city' },
          { text: 'Улица', value: 'street' },
          { text: 'Дом', value: 'house' },
          { text: 'Подъезд', value: 'perch' },
          { text: 'Этаж', value: 'floor' },
          { text: 'Квартира', value: 'apartment' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        headersOrder: [
          { text: 'ИД', value: 'id' },
          { text: 'Дата заказа', value: 'date' },
          { text: 'Дата доставки', value: 'delivery_date' },
          { text: 'Статус', value: 'status' },
          { text: 'Комментарий', value: 'comment' },
          { text: 'Адрес', value: 'address_id' },
          { text: 'Общая сумма', value: 'total' },
          { text: 'Покупатель', value: 'user_id' },
          { text: 'Товары', value: 'products' },
          { text: 'Действия', value: 'actions', sortable: false },
        ],
        items: [],
        itemsOrder: [],
        editedIndex: -1,
        editedIndexOrder: -1,
        editedItem: {
          country: '',
          city: '',
          street: '',
          house: '',
          perch: '',
          floor: '',
          apartment: '',
        },
        defaultItem: {
          country: '',
          city: '',
          street: '',
          house: '',
          perch: '',
          floor: '',
          apartment: '',
        },
        editedItemOrder: {
          date: '',
          delivery_date: '',
          status: '',
          comment: '',
          user_id: '',
          address_id: '',
          products: '',
        },
        defaultItemOrder: {
          date: '',
          delivery_date: '',
          status: '',
          comment: '',
          user_id: '',
          address_id: '',
          products: '',
        },
        textRules: [
          v => !!v || 'Введите значение!',
          v => (v && v.length <= 25) || 'Текст должен быть длинной менее 25-х символов'
        ],
        numberRules: [
          v => !!v || 'Введите значение!',
          v => (v && !isNaN(v) && v > 0 && v < 1000) || 'Введите число от 0 до 1000!',
        ],
        selectedRules: [
          v => !!v || 'Выберите элементы!',
        ],
      }
    },

    created () {
      this.initialize();
      this.initializeOrder();
    },
    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Создание адреса' : 'Редактирование адреса'
      },
      formTitleOrder () {
        return 'Просмотр заказа'
      },
      isLoading: function () { return this.$store.getters.isLoading },
      user_id: function () { return this.$store.getters.isLoggedId }
    },
    watch: {
      dialog (val) {
        val || this.close()
      },
    },
    methods: {
      initialize () {

        console.log(this.user_id)
        this.$store.dispatch('getOneEntity', { id: this.user_id, entity: 'users' })
          .then((resp) => {
              this.user = resp.data;
          })
          .catch(err => { this.$router.push('/')})

        this.$store.dispatch('getEntity', { data: {order: 'id asc' }, entity: 'addresses/index-user' })
          .then((resp) => {
            this.items = resp.data
          })
          .catch(err => this.$router.push('/'))
      },
      initializeOrder  () {
        this.$store.dispatch('getEntity', { data: {order: 'id asc' }, entity: 'orders/index-user' })
          .then((resp) => {
            this.itemsOrder = resp.data
          })
          .catch(err => this.$router.push('/'))
      },

      editItem (item) {
        this.editedIndex = this.items.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      editItemOrder (item) {
        this.editedIndexOrder = this.itemsOrder.indexOf(item)
        this.editedItemOrder = Object.assign({}, item)
        this.dialogOrder = true
      },

      createItem () {
        this.editedIndex = -1
        this.dialog = true
      },

      deleteItem (item) {
        this.$store.dispatch('deleteEntity', { entity: 'addresses/delete-user', id: item.id })
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

      deleteItems () {
        for (let i = 0; i < this.selected.length; i++) {
          this.deleteItem(this.selected[i])
        }
      },

      deleteOrderItem (item) {
        let order = {
          date: item.date,
          delivery_date: item.delivery_date,
          status: 'Отменён',
          comment: item.comment,
          address_id: item.address_id,
          user_id: this.user_id,
        };
        this.$store.dispatch('putEntity', { data: order, id: item.id, entity: 'orders/update-user' })
          .then((resp) => {this.closeOrder ()})
          .catch(err => {this.error = 'Ошибка обновления'}).finally(() => this.initializeOrder())

      },

      deleteOrderItems () {
        for (let i = 0; i < this.selectedOrder.length; i++) {
          this.deleteOrderItem(this.selectedOrder[i])
        }
      },

      close () {
        this.editedEntity = {}
        this.$refs.form.reset()
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      closeOrder () {
        this.$refs.formOrder.reset()
        this.dialogOrder = false
        this.$nextTick(() => {
          // this.editedItemOrder = Object.assign({}, this.defaultItemOrder)
          this.editedIndexOrder = -1
        })
      },

      save () {
        let address = {
          country: this.editedItem.country,
          city: this.editedItem.city,
          street: this.editedItem.street,
          house: this.editedItem.house,
          perch: this.editedItem.perch,
          floor: this.editedItem.floor,
          apartment: this.editedItem.apartment,
          user_id: this.user_id
        }
        if (this.editedIndex > -1) {
          this.$store.dispatch('putEntity', { data: address, id: this.editedItem.id, entity: 'addresses/update-user' })
            .then((resp) => {
              this.initialize()
            })
            .catch(err => {
                this.initialize()
                this.error = 'Ошибка обновления'
              }
            )
        } else {
          this.$store.dispatch('postEntity', { entity: 'addresses/store-user', data: address })
            .then((resp) => {
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
      getTextByValueOrder (value, items) {
        for (let i = 0; i < items.length; i++) {
          if (items[i].id === value) {
            return `${items[i].city}, ул.${items[i].street} ${items[i].house}`
          }
        }
        return value
      },
      getProducts (products) {
        let stringProducts = '';
        for (let i = 0; i < products.length; i++) {
          stringProducts += products[i].name;
          if(i < products.length -1) stringProducts += ', ';
        }
        return stringProducts;
      },
    }
  }
</script>

<style>
  .theme--light.v-input--is-disabled, .theme--light.v-input--is-disabled input, .theme--light.v-input--is-disabled textarea {
     color: rgba(0,0,0, 1) !important;
  }

  .v-chip--disabled {
    opacity: 1!important;
  }
</style>