<template>
  <v-container grid-list-md text-xs-center class="mt-5 mb-5">

    <v-app-bar class="gradient-project main-header" dark>
      <div class="hidden-md-and-up w-100">
        <v-spacer/>
        <v-menu offset-y class="gradient-project">
          <template v-slot:activator="{ on: menu, attrs }">
            <v-tooltip bottom style="width: 100%;">
              <template v-slot:activator="{ on: tooltip }">
                <v-btn
                    color="#FF9765"
                    dark
                    v-bind="attrs"
                    v-on="{ ...tooltip, ...menu }"
                    style="width: 100%;"
                >Категории
                </v-btn>
              </template>
            </v-tooltip>
          </template>
          <v-list class="gradient-project">
            <v-list-item @click="goTo(null)" class="gradient-project">
              <v-list-item-title>ВСЕ ТОВАРЫ</v-list-item-title>
            </v-list-item>
            <v-list-item
                v-for="category in fullCategories"
                :key="category.id"
                @click="goTo(category.id)"
                class="gradient-project"
            >
              <v-list-item-title>{{ category.name }}</v-list-item-title>
            </v-list-item>
            <v-divider v-if="subCategories.length > 0" class="gradient-project"></v-divider>
            <v-subheader v-if="subCategories.length > 0">Подкатегории</v-subheader>
            <v-list-item
                v-for="category in subCategories"
                :key="category.id"
                @click="goTo(category.id)"
                class="gradient-project"
            >
              <v-list-item-title>{{ category.name }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
        <v-spacer/>
      </div>

      <v-toolbar-items class="hidden-sm-and-down" style="overflow: hidden">
        <v-btn class="gradient-project" elevation="0" @click="goTo (null)" style="text-decoration: none;">
          ВСЕ ТОВАРЫ
        </v-btn>
        <v-btn
            class="gradient-project"
            elevation="0"
            link v-for="category in fullCategories"
            :key="category.id"
            @click="goTo (category.id)"
            style="text-decoration: none;"
        >
          {{ category.name }}
        </v-btn>
      </v-toolbar-items>
    </v-app-bar>

    <v-layout row wrap>
      <v-flex xs3 class="hidden-sm-and-down">
        <v-layout row wrap>
          <v-flex xs12 v-if="subCategories.length > 0">
            <v-card class="elevation-12 w-100">
              <v-toolbar dark class="gradient-project" height="40">
                <v-toolbar-title class="mx-auto text-center" style="font-size: 1.06rem">
                  Подкатегории
                </v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <v-list>
                  <v-list-item
                      v-for="category in subCategories"
                      :key="category.id"
                      @click="goTo(category.id)"
                  >
                    <v-list-item-title>
                      <span v-if="category.id != id">{{ category.name }}</span>
                      <strong v-if="category.id == id">{{ category.name }}</strong>
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-card-text>
            </v-card>
          </v-flex>

          <v-flex xs12 v-if="sortItems.length > 0">
            <v-card class="elevation-12 w-100">
              <v-toolbar dark class="gradient-project" height="40">
                <v-toolbar-title class="mx-auto text-center" style="font-size: 1.06rem">
                  Сортировать по
                </v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <v-list>
                  <v-list-item
                      v-for="sortItem in sortItems"
                      :key="'1' + sortItem.value[0] + sortItem.value[1]"
                      @click="goToSort(sortItem.value)"
                  >
                    <v-list-item-title>
                      <span v-if="sortItem.value[0] != orderBy || sortItem.value[1] != orderType">{{ sortItem.text }}</span>
                      <strong v-if="sortItem.value[0] == orderBy && sortItem.value[1] == orderType">{{ sortItem.text }}</strong>
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-card-text>
            </v-card>
          </v-flex>

          <v-flex xs12>
            <v-btn
                class="gradient-project"
                dark
                v-bind="attrs"
                v-on="{ ...tooltip, ...menu }"
                style="width: 100%;"
            >
              Филтры
            </v-btn>
          </v-flex>
        </v-layout>
      </v-flex>

      <v-flex xs12 md9>
        <v-layout row wrap>
          <v-flex xs12 mt-5>
            <h2 class="display-1 title-admin text-center text--secondary">Товары</h2>
          </v-flex>
          <v-flex xs12 mt-3 px-5 align-items-center>
            <v-card
                class="mx-auto"
            >
              <v-list v-if="products.length > 0">
                <v-list-item
                    v-for="item in products"
                    :key="item.name"
                    :to="'/products/' + item.id"
                    three-line
                >
                  <v-list-item-avatar height="150px" width="150px" style="border-radius: 0!important;">
                    <v-img :src="'/storage/products/' + item.slug + '-1.png'"/>
                  </v-list-item-avatar>

                  <v-list-item-content>
                    <strong>
                      <v-list-item-title class="product-list-content" v-text="item.name"/>
                    </strong>
                    <v-list-item-content class="post-text product-list-content" v-html="item.description"/>
                    <v-list-item-content v-if="item.sales.length > 0">
                      <strong>
                        <big class="red--text">{{ Math.floor((item.price - (item.price * item.sales[0].percent /
                          100))*100)/100 }}</big>
                        <span class="text-decoration-line-through">{{item.price}}</span>
                      </strong>
                      <big>{{ `(${item.sales[0].date_start} - ${item.sales[0].date_end})` }}</big>
                    </v-list-item-content>
                    <v-list-item-content v-if="item.sales.length <= 0">
                      <strong>
                        <span>{{item.price}}</span>
                      </strong>
                    </v-list-item-content>
                  </v-list-item-content>

                  <v-list-item-action>
                    <v-btn color="#FF9765" x-large fab text dark>
                      <big class="text-decoration-none">
                        {{ item.ranking }}
                      </big>
                      <v-icon>mdi-star</v-icon>
                    </v-btn>
                  </v-list-item-action>
                </v-list-item>
              </v-list>
              <h2 class="text--secondary text-center" v-if="products.length < 0">Товаров не обнаружено</h2>
            </v-card>

          </v-flex>
          <v-pagination
              v-model="page"
              :length="total"
              :total-visible="7"
              circle
              color="#FF9765"
              @input="runToPage"
          />
        </v-layout>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  export default {
    data () {
      return {
        page: 1,
        drawer: false,
        id: null,
        orderBy: 1,
        orderType: 1,
        total: 1,
        search: '',
        products: [],
        fullCategories: [],
        subCategories: [],
        sortItems: [
          {text: 'Сначала новые', value: ['id', 'desc']},
          {text: 'Сначала старые', value: ['id', 'asc']},
          {text: 'Сначала рейтинговые', value: ['ranking', 'desc']},
          {text: 'Сначала нерейтинговые', value: ['ranking', 'asc']},
          {text: 'Сначала дорогие', value: ['price', 'desc']},
          {text: 'Сначала дешёвые', value: ['price', 'asc']},
        ],
      }
    },
    created () {
      this.initialize()
    },
    methods: {
      initialize () {
        this.page = Number.parseInt(this.$route.query.page) || 1
        this.orderBy = (this.$route.query.orderBy || 'id')
        this.orderType = (this.$route.query.orderType || 'desc')
        let offset = (this.page - 1) * 10
        let date = new Date().toISOString().substr(0, 10)
        this.search = (this.$route.query.search || '')
        this.id = Number.parseInt(this.$route.params.id) || null

        let dataCount = { count: 'true' }
        let data = {
          offset, limit: 10,
          order: this.orderBy + ' ' + this.orderType,
          with: 'sales',
          withWhere: `sales.date_start < '${date}' AND sales.date_end > '${date}'`
        }

        if (this.id !== null) {
          dataCount.where = `products.category_id = ${this.id}`
          data.where = `products.category_id = ${this.id}`
        } else if (this.search != '') {
          dataCount.search = this.search
          data.search = this.search
        }

        this.$store.dispatch('getEntity', { entity: 'products', data: dataCount })
          .then((resp) => {
            this.total = Math.ceil(resp.data / 10)
          })
          .catch(err => this.$router.push('/'))

        this.$store.dispatch('getEntity', { entity: 'categories', data: { where: 'parent_id IS NULL' } })
          .then((resp) => {
            this.fullCategories = resp.data
          })
          .catch(err => this.$router.push('/'))

        this.$store.dispatch('getEntity', { entity: 'categories', data: { where: 'parent_id = ' + this.id } })
          .then((resp) => {
            this.subCategories = resp.data
          })
          .catch(err => this.$router.push('/'))

        this.$store.dispatch('getEntity', { entity: 'products', data })
          .then((resp) => {
            this.products = resp.data
          })
          .catch(err => {
            this.$router.push('/')
          })
      },
      runToPage (page) {
        let id = this.id > 0 ? '/' + this.id : '';
        this.$router.push(`/categories${id}?page=${page}&orderBy=${this.orderBy}&orderType=${this.orderType}`)
        this.initialize()
      },
      goTo (id) {
        let route = this.$router.resolve({name: 'categories-many', params: {id}});
        window.open(route.href, '_self');
      },
      goToSort (value) {
        this.orderBy = value[0];
        this.orderType = value[1];
        this.runToPage(this.page);
      }
    }
  }
</script>

<style>
  .v-pagination--circle .v-pagination__item,
  .v-pagination--circle .v-pagination__more,
  .v-pagination--circle .v-pagination__navigation {
    border: 1px solid !important;
  }

  .post-text {
    overflow: hidden !important;
    text-overflow: ellipsis;
    display: -webkit-box !important;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }
</style>
