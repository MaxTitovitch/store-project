<template>
  <v-container fluid class="mt-5 mb-5">
    <v-layout align-center justify-center>
      <v-flex xs10>
        <v-card class="elevation-12 ">
          <v-toolbar dark class="gradient-project">
            <v-toolbar-title class="w-100 text-center">Статья: {{ post.name }}</v-toolbar-title>
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
        post: { user: {} },
      }
    },
    created () {
      let id = Number.parseInt(this.$route.params.id);

      this.$store.dispatch('getOneEntity', {entity: 'posts', id})
        .then((resp) => {
          this.post= resp.data;
          this.image=`/storage/posts/${this.post.slug}.png`;
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
</style>