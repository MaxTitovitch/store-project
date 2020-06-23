<template>
  <div>
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
          <div>
            <big>{{ item.likes.length }}</big>
            <v-btn icon :color="isUserLikes(item.likes) !== false ? 'deep-orange' : 'gray'" :disabled="isLoggedId <= 0" @click="changeLike(item)">
              <v-icon>mdi-thumb-up</v-icon>
            </v-btn>
          </div>
        </v-list-item-action>
      </v-list-item>
    </v-list>
    <v-pagination
        v-model="currentPage"
        :length="total"
        :total-visible="7"
        circle
        color="#FF9765"
        @input="runToPage"
    />
  </div>
</template>

<script>
  export default {
    data () {
      return {
        total: 1,
        comments: [],
        newComment: '',
        currentPage: 1,
        valid: false,
        user: {},
        textRules: [
          v => (v && v.length >= 10) || 'Текст должен быть длинной более 10-х символов'
        ],
      }
    },
    props: ['page', 'entity_type', 'entity_id', 'entity_type_href'],
    computed: {
      isLoggedId: function () { return this.$store.getters.isLoggedId },
      isLoggedRole: function () { return this.$store.getters.isLoggedRole },
    },
    created () {
      this.currentPage = this.page;
      this.$store.dispatch('getOneEntity', { entity: 'users', id: this.isLoggedId })
      .then((resp) => {
        this.user = resp.data
      })
      .catch(err => {this.$router.push('/')})
      this.initializeComment();
    },
    methods: {
      runToPage(page) {
        this.$router.push(`/${this.entity_type_href}/${this.entity_id}?page=` + page).catch(()=>{});;
        this.initializeComment();
      },
      initializeComment () {
        let offset = (this.currentPage - 1) * 10
        let data = { where: `entity_id = ${this.entity_id} AND entity_type="${this.entity_type}"`, with: ['user', 'likes'], offset, limit: 10, order: 'id desc' }
        this.$store.dispatch('getEntity', { entity: 'comments', data })
          .then((resp) => {
            this.comments = resp.data
            console.log(this.comments)
          })
          .catch(err => {this.$router.push('/')})

        let newData = {count: 'true', where: `entity_id = ${this.entity_id} AND entity_type="${this.entity_type}"`}
        this.$store.dispatch('getEntity', { entity: 'comments', data: newData })
          .then((resp) => {
            this.total= Math.ceil(resp.data / 10)
          })
          .catch(err => {this.$router.push('/')})
      },
      saveComment () {
        let comment = {
          text: this.newComment,
          date: new Date().toISOString().substr(0, 10),
          user_id: this.isLoggedId,
          entity_type: this.entity_type,
          entity_id: this.entity_id
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
      goTo (path) {
        this.$router.push(path)
      },
      isUserLikes (likes) {
        for (let i = 0; i < likes.length; i++) {
          if (likes[i].user_id == this.isLoggedId) {
            return likes[i].id;
          }
        }
        return false;
      },
      changeLike (comment) {
        if(this.isLoggedId > 0) {
          let likeId = this.isUserLikes(comment.likes);
          if(!likeId) {
            let like = { user_id: this.isLoggedId, entity_type: 'comment', entity_id: comment.id };
            this.$store.dispatch('postEntity', { entity: 'likes/store-user', data: like })
              .then((resp) => {
                comment.likes.push(resp.data);
              }).catch(err => {this.error = 'Ошибка добавления'})
          } else {
            console.log(likeId)
            this.$store.dispatch('deleteEntity', { entity: 'likes/delete-user', id: likeId })
              .then((resp) => {
                comment.likes = comment.likes.filter(function( item ) {
                  return item.id !== likeId;
                });
              }).catch(err => {this.error = 'Ошибка удаления'})
          }
        }
      }
    }
  }
</script>

<style scoped>

  .pointer-cursor:hover {
    cursor: pointer;
  }
</style>