<template>
  <v-container fluid class="mt-5 mb-5">
    <v-layout class="flex-wrap" align-center justify-center>
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
                <v-spacer/>
                <big>{{ post.likes_count }}</big>
                <v-btn icon :color="userLike.length <= 0 ? 'gray' : 'deep-orange'" :disabled="isLoggedId <= 0" @click="changeLike()">
                  <v-icon>mdi-thumb-up</v-icon>
                </v-btn>
              </v-card-actions>
        </v-card>
      </v-flex>

      <v-flex xs10 mt-5 align-items-center>
        <v-list subheader class="product-list">
          <h3 class="text--secondary px-3">Комментарии</h3>
          <v-list-item two-line v-if="isLoggedId > 0">

            <v-list-item-avatar height="150px" width="150px" style="border-radius: 0!important;">
              <v-img
                  style="border-radius: 100%"
                  :src="user.slug ? '/storage/users/' + user.slug + '.png' : '/images/empty-user.jpg'"
                  onerror="if (this.src != '/images/empty-user.jpg') this.src = '/images/empty-user.jpg';"
              />
            </v-list-item-avatar>

            <v-list-item-content>

              <v-form v-model="valid" ref="form" validation>
                <v-textarea
                    color="#FF9765"
                    v-model="newComment"
                    :rules="textRules"
                    label="Комментарий"
                    filled
                    auto-grow
                />
              </v-form>
            </v-list-item-content>

            <v-list-item-action>
              <v-btn large dark color="#FF9765" :disabled="!valid" v-bind:dark="valid" @click="saveComment()">
                  <span class="text-decoration-none">
                    Отправить
                  </span>
              </v-btn>
            </v-list-item-action>
          </v-list-item>

          <v-list-item
              v-for="(item, index) of comments"
              :key="'products' + item.id"
              two-line
          >
            <v-list-item-avatar height="150px" width="150px" style="border-radius: 0!important;" >
              <v-img class="pointer-cursor" @click="goTo(`/users/${item.user.id}`)" style="border-radius: 100%" :src="'/storage/users/' + item.user.slug + '.png'"/>
            </v-list-item-avatar>

            <v-list-item-content>
              <strong class="pointer-cursor" @click="goTo(`/users/${item.user.id}`)">
                <v-list-item-title class="product-list-content" v-text="item.user.name + ' ' + item.user.last_name"/>
              </strong>
              <v-list-item-content class="post-text product-list-content" v-text="item.text"/>
              <small class="font-weight-bold">
                <v-list-item-content class="post-text product-list-content" v-html="item.date"/>
              </small>
            </v-list-item-content>

            <v-list-item-action v-if="item.user.id == isLoggedId || isLoggedRole === 'Главный администратор'">
              <v-btn small fab dark color="red" @click="removeComment(item.id)">
                <span class="text-decoration-none">x</span>
              </v-btn>
            </v-list-item-action>
          </v-list-item>
        </v-list>
        <v-pagination
            v-model="page"
            :length="total"
            :total-visible="7"
            circle
            color="#FF9765"
            @input="runToPage"
        />
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
        products: [],

        page: 1,
        total: 1,
        comments: [],
        newComment: '',
        valid: false,
        user: {},
        textRules: [
          v => (v && v.length >= 10) || 'Текст должен быть длинной более 10-х символов'
        ],
        userLike: false,
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

          if (this.isLoggedId) {
            this.$store.dispatch('getOneEntity', { entity: 'users', id: this.isLoggedId })
              .then((resp) => {
                this.user = resp.data
              })
              .catch(err => {this.$router.push('/')})
            this.$store.dispatch('getEntity', { entity: 'likes', data: {where: `entity_type = "post" AND entity_id = ${this.post.id} AND user_id = ${this.isLoggedId}`} })
              .then((resp) => {
                this.userLike = resp.data || false;
              })
              .catch(err => {this.$router.push('/')})
          }
          this.initializeComment()
        })
        .catch(err => this.$router.push('/'));
    },
    computed: {
      isLoggedId: function () { return this.$store.getters.isLoggedId },
      isLoggedRole: function () { return this.$store.getters.isLoggedRole },
    },
    methods: {
      runToPage(page) {
        this.$router.push(`/tops/${this.post.id}?page=` + page).catch(()=>{});
        this.initializeComment();
      },
      initializeComment () {
        this.page = Number.parseInt(this.$route.query.page) || 1
        let offset = (this.page - 1) * 10
        let data = { where: `entity_id = ${this.post.id} AND entity_type="post"`, with: 'user', offset, limit: 10, order: 'id desc' }

        this.$store.dispatch('getEntity', { entity: 'comments', data })
          .then((resp) => {
            this.comments = resp.data
          })
          .catch(err => {this.$router.push('/')})

        let newData = {count: 'true', where: `entity_id = ${this.post.id} AND entity_type="post"`}
        this.$store.dispatch('getEntity', { entity: 'comments', data: newData })
          .then((resp) => {
            this.total= Math.ceil(resp.data / 10)
          })
          .catch(err => {this.$router.push('/')})
      },
      goTo (path) {
        this.$router.push(path)
      },
      saveComment () {
        let comment = {
          text: this.newComment,
          date: new Date().toISOString().substr(0, 10),
          user_id: this.isLoggedId,
          entity_type: 'post',
          entity_id: this.post.id
        }
        this.$store.dispatch('postEntity', { entity: 'comments/store-user', data: comment })
          .then((resp) => {})
          .catch(err => {this.error = 'Ошибка добавления'})
          .finally(() => {
            this.newComment = '';
            this.$refs.form.reset();
            this.initializeComment()
          })
      },
      removeComment (id) {
        this.$store.dispatch('deleteEntity', { entity: 'comments/delete-user', id })
          .then((resp) => {}).catch(err => {this.error = 'Ошибка удаления'})
          .finally(() => {
            this.initializeComment()
          })
      },
      changeLike () {
        if(this.isLoggedId > 0) {
          if(this.userLike.length <= 0) {
            let like = { user_id: this.isLoggedId, entity_type: 'post', entity_id: this.post.id };
            this.$store.dispatch('postEntity', { entity: 'likes/store-user', data: like })
              .then((resp) => {
                this.userLike = [resp.data || false];
                this.post.likes_count++;
              }).catch(err => {this.error = 'Ошибка добавления'})
          } else {
            this.$store.dispatch('deleteEntity', { entity: 'likes/delete-user', id: this.userLike[0].id })
              .then((resp) => {
                this.userLike = [];
                this.post.likes_count--;
              }).catch(err => {this.error = 'Ошибка удаления'})
              .finally(() => {})
          }
        }
      }
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

  .pointer-cursor:hover {
    cursor: pointer;
  }
</style>