<template>
    <div class="page-404 container-fluid" >

    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Pages not found.')
        }
    }
</script>

<style>
    .page-404 {
        height: 100%;
        width: 100%;
        background: url(/404.gif);
        background-size: 100% 100%;
    }
</style>
