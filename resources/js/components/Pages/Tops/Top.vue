<template>
  <v-container fluid class="mt-5 mb-5">
    <v-layout align-center justify-center>
      <v-flex xs10>
        <v-card class="elevation-12 ">
          <v-toolbar dark class="gradient-project">
            <v-toolbar-title class="w-100 text-center">Топ: {{ post.name }}</v-toolbar-title>
          </v-toolbar>
              <v-img
                  class="white--text align-end"
                  height="200px"
                  :src="image"
              >
                <v-card-title class="text-up-line">
                  <h1>{{ post.name }}</h1>
                </v-card-title>
              </v-img>


              <v-card-text class="text--primary mt-1">
                <div v-html="post.text"></div>
              </v-card-text>

              <v-card-text>
                <v-list subheader class="product-list">
                  <h2 class="text--secondary">{{ post.top.name }}</h2>

                  <v-list-item
                      v-for="(item, index) of products"
                      :key="'products' + item.id"
                      :to="'/products/' + item.id"
                      three-line
                  >
                    <v-list-item-avatar height="150px" width="150px" style="border-radius: 0!important;">
                      <v-img :src="'/storage/products/' + item.slug + '-1.png'"/>
                    </v-list-item-avatar>

                    <v-list-item-content class="product-list-content">
                      <strong><v-list-item-title v-text="item.name"/></strong>
                      <v-list-item-content class="post-text" v-text="item.price"/>
                    </v-list-item-content>

                    <v-list-item-action>
                      <v-btn color="#FF9765" x-large fab dark >
                        <span class="text-decoration-none">
                          {{ index + 1 }}
                        </span>
                      </v-btn>
                    </v-list-item-action>
                  </v-list-item>
                </v-list>
              </v-card-text>

              <v-card-actions>
                <v-btn color="#FF9765" text small>
                  {{ post.date }}
                </v-btn>
              </v-card-actions>
              <v-card-actions>

                <v-btn color="#FF9765" text small :to="`/users/${post.user_id}`">
                  {{ `${post.user.name} ${post.user.last_name}`  }}
                </v-btn>
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
        image: '/images/empty-post.jpg',
        post: { user: {}, top: {} },
        products: []
      }
    },
    created () {
      let id = Number.parseInt(this.$route.params.id);

      this.$store.dispatch('getOneEntity', {entity: 'posts', id})
        .then((resp) => {
          this.post= resp.data;
          this.image=`/storage/posts/${this.post.slug}.png`;
          this.products = this.post.top.products.sort(function (a, b) {
            if (a.pivot.top > b.pivot.top) {return 1;}
            if (a.pivot.top < b.pivot.top) {return -1;}
            return 0;
          });
          console.log(this.products)
        })
        .catch(err => this.$router.push('/'));
    },
    methods: {
      initialize() {
      },
    }
  }
</script>

<style>
  .text-up-line {
    text-shadow: 2px 0 2px #000,
    0 2px 2px #000,
    -2px 0 2px #000,
    0 -2px 2px #000;
  }


  a:hover {
    text-decoration: none;
  }
  .product-list a:hover .product-list-content{
    text-decoration: underline;
  }
</style>