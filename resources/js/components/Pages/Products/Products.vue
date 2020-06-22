<template>
  <v-container grid-list-md text-xs-center class="mt-5 mb-5">
    <v-layout row wrap>
      <v-flex xs12 mt-5>
        <h2 class="display-1 title-admin text-center text--secondary">Товары</h2>
      </v-flex>
      <v-flex xs12 mt-3 px-5 align-items-center>
        <v-card
            class="mx-auto"
        >
          <v-list>
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
                    <big class="red--text">{{ Math.floor((item.price - (item.price * item.sales[0].percent / 100))*100)/100 }}</big>
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
                      {{  item.ranking }}
                    </big>
                  <v-icon>mdi-star</v-icon>
                </v-btn>
              </v-list-item-action>
            </v-list-item>
          </v-list>
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
  </v-container>
</template>

<script>
  export default {
    data () {
      return {
        page: 1,
        orderBy: 1,
        orderType: 1,
        total: 1,
        products: [],
      }
    },
    created () {
      this.initialize();
    },
    methods: {
      initialize() {  //
        this.page = Number.parseInt(this.$route.query.page) || 1;
        this.orderBy = (this.$route.query.orderBy || 'id');
        this.orderType = (this.$route.query.orderType || 'desc');
        let offset = (this.page - 1) * 10;
        let date = new Date ().toISOString().substr(0, 10);

        this.$store.dispatch('getEntity', {entity: 'products', data: { count: 'true' }})
          .then((resp) => {
            this.total= Math.ceil(resp.data / 10);
          })
          .catch(err => this.$router.push('/'));

        this.$store.dispatch('getEntity', {entity: 'products', data: {
          offset, limit: 10, order: this.orderBy + ' ' + this.orderType, with: 'sales', withWhere: `sales.date_start < '${date}' AND sales.date_end > '${date}'`
        }})
          .then((resp) => {
            this.products = resp.data
          })
          .catch(err => {
            this.$router.push('/')
          });
      },
      runToPage(page) {
        this.$router.push(`/categories?page=${page}&orderBy=${this.orderBy}&orderType=${this.orderType}` );
        this.initialize();
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
