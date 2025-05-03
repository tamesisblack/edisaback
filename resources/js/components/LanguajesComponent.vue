
<template>
  <div>
    <!-- <v-menu offset-y style="float: right; position: relative; top: -50px !important;">
    <template v-slot:activator="{ on, attrs }">
        <v-btn color="primary" text fab small dense v-bind="attrs" v-on="on" > <v-icon dark>mdi-earth</v-icon> </v-btn>
    </template>
    <v-list>
        <v-list-item v-for="(language, index) in languages" :key="index" >
            <v-list-item-title @click="changeLanguage(language.locale)">{{ language.title }}</v-list-item-title>
        </v-list-item>
    </v-list>
    </v-menu> -->


<!-- 
    <v-btn-toggle v-model="toggle_multiple" dense small dark multiple v-for="(language, index) in languages" :key="index" >
        <v-btn small dense @click="changeLanguage(language.locale)">{{ language.title }}</v-btn>
    </v-btn-toggle> -->


    <v-avatar @click="changeLanguage(languages[0].locale)" style="cursor: pointer; width: 35px; height: 35px;"> <img src="https://i.pinimg.com/originals/59/f8/0b/59f80bd28387cff4b975612e4499d12d.png" alt="Español" > </v-avatar>

    <v-avatar @click="changeLanguage(languages[1].locale)" style="cursor: pointer; width: 35px; height: 35px;"> <img src="https://render.fineartamerica.com/images/images-profile-flow/400/images-medium-large-5/united-kingdom-union-jack-england-britain-flag-vintage-distressed-finish-design-turnpike.jpg" alt="English" > </v-avatar>

  </div>
</template>

<script>
import { i18n } from '../../plugins/i18n'
export default {
  name: 'LanguageComponent',
  data: () => ({
    languages: [
      { locale: 'es', title: 'Español' },
      { locale: 'en', title: 'Inglés' }
    ]
  }),
  methods: {
    changeLanguage(locale) {
      localStorage.idioma_bingo = locale
      i18n.locale = locale
    }
  }
}
</script>