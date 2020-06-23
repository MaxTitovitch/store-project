<template>
  <v-container fluid class="mt-5 mb-5">
    <v-layout align-center justify-center>
      <v-flex xs10>
        <v-card class="elevation-12 ">
          <v-toolbar dark class="gradient-project">
            <v-toolbar-title class="w-100 text-center">Оформление Заказа</v-toolbar-title>
          </v-toolbar>
          <v-img
              class="white--text align-end"
              height="200px"
              :src="image"
          >
            <v-card-title class="text-up-line">
              <h1>Оформление Заказа</h1>
            </v-card-title>
          </v-img>

          <v-card-text class="text--primary mt-1">
            <v-form v-model="valid" ref="form" validation>
              <v-row>
                <v-col cols="12" sm="12" md="12">
                  <v-textarea
                      color="#FF9765"
                      v-model="order.comment"
                      :rules="commentRules"
                      :label="!isLoggedIn ? 'Укажите контактные данные для связи' : 'Комментарий к заказу'"
                      filled
                      auto-grow
                  />
                </v-col>

                <v-col cols="12" sm="6" md="6">
                  <v-menu
                      ref="menu"
                      v-model="menu"
                      :close-on-content-click="false"
                      transition="scale-transition"
                      offset-y
                      min-width="290px"
                      color="#ff9966"
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                          v-model="order.delivery_date"
                          label="Дата доставки"
                          readonly
                          v-on="on"
                          :rules="deliveryDateRules"
                          color="#ff9966"
                      />
                    </template>
                    <v-date-picker
                        v-model="order.delivery_date"
                        scrollable
                        locale="ru-ru"
                        :min="new Date().toISOString().substr(0, 10)"
                        :max="getNextYear()"
                        first-day-of-week="1"
                        header-color="#ff9765"
                    >
                      <v-spacer></v-spacer>
                      <v-btn text color="primary" @click="menu = false">Отмена</v-btn>
                      <v-btn text color="primary" @click="$refs.menu.save(order.delivery_date)">ОК</v-btn>
                    </v-date-picker>
                  </v-menu>
                </v-col>

                <v-col cols="12" md="6" v-if="addressItems.length > 0 && isLoggedIn">
                  <v-switch
                      v-model="isAddressId"
                      :label="`${isAddressId ? 'Сохраненный адрес' : 'Ввести данные'}`"
                      color="#ff9966"
                  />
                </v-col>

                <v-col cols="12" sm="6" md="6" v-if="(isAddressId || addressItems.length <= 0) && isLoggedIn">
                  <v-select
                      :items="addressItems"
                      item-text="text"
                      item-value="value"
                      v-model="order.address_id"
                      item-color="#ff9966"
                      label="Адрес"
                      clearable
                      color="#ff9966"
                      :rules="selectedRules"
                  />
                </v-col>

              </v-row>
              <v-row v-if="!isAddressId || addressItems.length <= 0">
                <v-col cols="12" sm="6" md="3">
                  <v-text-field
                      color="#FF9765"
                      v-model="address.country"
                      :rules="textRules"
                      label="Страна"/>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-text-field
                      color="#FF9765"
                      v-model="address.city"
                      :rules="textRules"
                      label="Город"/>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-text-field
                      color="#FF9765"
                      v-model="address.street"
                      :rules="textRules"
                      label="Улица"/>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-text-field
                      color="#FF9765"
                      v-model="address.house"
                      :rules="textRules"
                      label="Дом"/>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-text-field
                      color="#FF9765"
                      v-model="address.perch"
                      :rules="numberRules"
                      label="Подъезд"/>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-text-field
                      color="#FF9765"
                      v-model="address.floor"
                      :rules="numberRules"
                      label="Этаж"/>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-text-field
                      color="#FF9765"
                      v-model="address.apartment"
                      :rules="numberRules"
                      label="Квартира"/>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>

          <v-card-text>
            <v-list subheader class="product-list">
              <h2 class="text--secondary">Товары</h2>

              <v-list-item
                  v-for="(item, index) of products"
                  :key="'products' + item.product.id"
                  three-line
              >
                <v-list-item-avatar height="150px" width="150px" style="border-radius: 0!important;" :to="'/products/' + item.product.id">
                  <v-img :src="'/storage/products/' + item.product.slug + '-1.png'"/>
                </v-list-item-avatar>

                <v-list-item-content :to="'/products/' + item.product.id">
                  <strong>
                    <v-list-item-title class="product-list-content" v-text="item.product.name"/>
                  </strong>
                  <v-list-item-content class="post-text product-list-content" v-html="item.product.description"/>
                  <v-list-item-content v-if="item.product.sales.length > 0">
                    <strong>
                      <big class="red--text">{{ Math.floor((item.product.price - (item.product.price *
                        item.product.sales[0].percent / 100))*100)/100 }} руб.</big>
                      <span class="text-decoration-line-through">{{item.product.price}} руб.</span>
                    </strong>
                    <strong>
                      <span>ВСЕГО:</span>
                      <big class="red--text">{{ Math.floor((item.product.price - (item.product.price *
                        item.product.sales[0].percent / 100))*100)/100*item.quantity }} руб.</big>
                    </strong>
                  </v-list-item-content>
                  <v-list-item-content v-if="item.product.sales.length <= 0">
                    <strong>
                      <span>{{item.product.price}} руб.</span>
                    </strong>
                    <strong>
                      <span>ВСЕГО:</span>
                      <big class="red--text">{{ item.product.price * item.quantity }} руб.</big>
                    </strong>
                  </v-list-item-content>
                </v-list-item-content>

                <v-list-item-action>
                  <v-btn color="#FF9765" x-large fab text dark>
                    <big class="text-decoration-none">
                      Кол-во: {{ item.quantity }}
                    </big>
                  </v-btn>
                  <div>
                    <v-btn color="green" small fab dark @click="addOneItem(item.product.id)">
                      <span class="text-decoration-none">
                        +
                      </span>
                    </v-btn>
                    <v-btn color="red" small fab dark @click="removeOneItem(item.product.id)">
                      <span class="text-decoration-none">
                        -
                      </span>
                    </v-btn>
                  </div>
                </v-list-item-action>
              </v-list-item>

              <v-list-item three-line>
                <v-list-item-content>
                  <v-list-item-content class="headline">
                    <strong>
                      <span>ИТОГО:</span>
                      <big class="red--text">{{getTotal()}} руб</big>
                    </strong>
                  </v-list-item-content>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card-text>

          <v-card-actions>
            <v-spacer/>
            <v-btn dark class="gradient-project px-5" text x-large
                   @click="save"
                   :disabled="!valid"
                   v-bind:dark="valid"
            >
              Заказать
            </v-btn>
            <v-btn dark color="#FF5F66" text @click="clearStore">Очистить</v-btn>
          </v-card-actions>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  export default {
    data () {
      return {
        id: null,
        valid: false,
        menu: false,
        isAddressId: true,
        addressItems: [],
        order: { comment: '', delivery_date: new Date().toISOString().substr(0, 10), address_id: '' },
        address: {
          country: '',
          city: '',
          street: '',
          house: '',
          perch: '',
          floor: '',
          apartment: '',
        },
        image: '/images/order.png',
        commentRules: [
          v => (v.length === 0 || v.length >= 10) || 'Описание должно быть длинной более 10-х символов',
          v => ((this.isAddressId || this.isLoggedIn) || !!v) || 'Введите данные для связи'
        ],
        deliveryDateRules: [
          v => !!v || 'Введите дату!',
        ],
        selectedRules: [
          v => !!v || 'Выберите элементы!',
        ],
        textRules: [
          v => !!v || 'Введите значение!',
          v => (v && v.length <= 25) || 'Текст должен быть длинной менее 25-х символов'
        ],
        numberRules: [
          v => !!v || 'Введите значение!',
          v => (v && !isNaN(v) && v > 0 && v < 1000) || 'Введите число от 0 до 1000!',
        ],
      }
    },
    computed: {
      isLoggedIn: function () { return this.$store.getters.isLoggedIn },
      isLoggedId: function () { return this.$store.getters.isLoggedId },
      isHave: function () {return this.$store.getters.isHave },
      products: function () {return this.$store.getters.cartProducts },
    },
    created () {
      this.initialize();

    },
    methods: {
      initialize() {
        if (!this.isHave) this.$router.push('/')
        this.isAddressId = this.isLoggedIn;

        if (this.isLoggedIn) {
          this.$store.dispatch('getEntity', { data: { order: 'id asc' }, entity: 'addresses/index-user' })
            .then((resp) => {
              let addressItems = resp.data
              for (let i = 0; i < addressItems.length; i++) {
                addressItems[i] = {
                  text: `${resp.data[i].city}, ул.${resp.data[i].street} ${resp.data[i].house}`,
                  value: resp.data[i].id
                }
                if (i === 0) {
                  this.order.address_id = addressItems[i].value
                }
              }
              this.addressItems = addressItems
            })
            .catch(err => this.$router.push('/'))
        }
      },
      addOneItem (id) {
        this.$store.dispatch('incrementItemQuantity', id)
        this.initialize();
      },
      removeOneItem (id) {
        this.$store.dispatch('decrementItemQuantity', id)
        this.initialize();
      },
      getTotal () {
        let total = 0
        for (let i = 0; i < this.products.length; i++) {
          let price = 0
          price = this.products[i].product.price
          if (this.products[i].product.sales.length > 0) {
            price = Math.floor((price - (price * this.products[i].product.sales[0].percent / 100)) * 100) / 100
          }
          total += price * this.products[i].quantity
        }
        return total
      },
      clearStore (path = '/') {
        this.$store.dispatch('clearCart', {})
        this.$router.push(path)
      },
      save () {
        if (!this.isAddressId && this.isLoggedIn) {
          let address = {
            country: this.address.country,
            city: this.address.city,
            street: this.address.street,
            house: this.address.house,
            perch: this.address.perch,
            floor: this.address.floor,
            apartment: this.address.apartment,
            user_id: this.isLoggedId
          }
          this.$store.dispatch('postEntity', { entity: 'addresses/store-user', data: address })
            .then((resp) => {
              this.saveOrder(resp.data.id)
            }).catch(err => {this.error = 'Ошибка заказа'})
        } else {
          this.saveOrder(this.isLoggedIn ? this.order.address_id : null, this.isLoggedIn ? false : this.address)
        }
      },
      saveOrder (addressId, address = false) {
        let order = {
          date: new Date().toISOString().substr(0, 10),
          delivery_date: this.order.delivery_date,
          status: 'В обработке',
          comment: this.order.comment,
          total: this.getTotal(),
          address_id: addressId,
          user_id: this.isLoggedId
        }
        if (address) {
          order.comment += `\nАдрес: г.${address.city} ${address.country}, ул.${address.street} ${address.house}
          кв. ${address.apartment}. Подъезд: ${address.perch}. Этаж: ${address.floor}.`
        }
        this.$store.dispatch('postEntity', { entity: 'orders/store-user', data: order })
          .then((resp) => {
            let ids = []
            for (let i = 0; i < this.products.length; i++) {
              ids.push(this.products[i].product.id)
            }
            this.$store.dispatch('putEntity', {
              entity: 'order-product/update-user',
              data: { 'products': ids },
              id: resp.data.id
            })
              .then((resp) => {
                this.clearStore(this.isLoggedIn ? '/personal' : '/')
              }).catch(err => { this.error = 'Ошибка заказа' })
          }).catch(err => { this.error = 'Ошибка заказа' })
      },
      getNextYear () {
        let date = new Date()
        date.setFullYear(date.getFullYear() + 1)
        return date.toISOString().substr(0, 10)
      },
    }
  }
</script>

<style>
  .text-up-line {
    text-shadow: 2px 0 2px #000000,
    0 2px 2px #000000,
    -2px 0 2px #000000,
    0 -2px 2px #000000;
  }

  a:hover {
    text-decoration: none;
  }

  .product-list a:hover .product-list-content {
    text-decoration: underline;
  }

  .text-decoration-line-through {
    text-decoration: line-through;
  }
</style>