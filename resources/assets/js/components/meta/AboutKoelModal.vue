<template>
  <div v-AudioStreaming-focus class="about text-secondary" data-testid="about-AudioStreaming" tabindex="0" @keydown.esc="close">
    <main>
      <div class="logo">
        <img alt="AudioStreaming's logo" src="@/../img/logo.svg" width="128">
      </div>

      <p class="current-version">AudioStreaming {{ currentVersion }}</p>

      <p v-if="shouldNotifyNewVersion" data-testid="new-version-about">
        <a :href="latestVersionReleaseUrl" target="_blank">
          A new version of AudioStreaming is available ({{ latestVersion }})!
        </a>
      </p>

      <p class="author">
        Made with ❤️ by
        <a href="https://github.com/topmantech" rel="noopener" target="_blank">Phan An</a>
        and quite a few
        <a href="https://github.com/AudioStreaming/core/graphs/contributors" rel="noopener" target="_blank">awesome</a>&nbsp;<a
          href="https://github.com/AudioStreaming/AudioStreaming/graphs/contributors" rel="noopener" target="_blank"
        >contributors</a>.
      </p>

      <div v-if="credits" class="credit-wrapper" data-testid="demo-credits">
        Music by
        <ul class="credits">
          <li v-for="credit in credits" :key="credit.name">
            <a :href="credit.url" target="_blank">{{ credit.name }}</a>
          </li>
        </ul>
      </div>

      <SponsorList />

      <p>
        Loving AudioStreaming? Please consider supporting its development via
        <a href="https://github.com/users/topmantech/sponsorship" rel="noopener" target="_blank">GitHub Sponsors</a>
        and/or
        <a href="https://topmantech.com/AudioStreaming" rel="noopener" target="_blank">OpenCollective</a>.
      </p>
    </main>

    <footer>
      <Btn data-testid="close-modal-btn" red rounded @click.prevent="close">Close</Btn>
    </footer>
  </div>
</template>

<script lang="ts" setup>
import { orderBy } from 'lodash'
import { onMounted, ref } from 'vue'
import { isDemo } from '@/utils'
import { useNewVersionNotification } from '@/composables'
import { http } from '@/services'

import SponsorList from '@/components/meta/SponsorList.vue'
import Btn from '@/components/ui/Btn.vue'

type DemoCredits = {
  name: string
  url: string
}

const credits = ref<DemoCredits[] | null>(null)

const {
  shouldNotifyNewVersion,
  currentVersion,
  latestVersion,
  latestVersionReleaseUrl
} = useNewVersionNotification()

const emit = defineEmits<{ (e: 'close'): void }>()
const close = () => emit('close')

onMounted(async () => {
  credits.value = isDemo() ? orderBy(await http.get<DemoCredits[]>('demo/credits'), 'name') : null
})
</script>

<style lang="scss" scoped>
.about {
  text-align: center;
  max-width: 480px;
  overflow: hidden;
  position: relative;

  main {
    padding: 1.8rem;

    p {
      margin: 1rem 0;
    }
  }

  footer {
    padding: 1rem;
    background: rgba(255, 255, 255, .02);
  }

  a {
    color: var(--color-text-primary);

    &:hover {
      color: var(--color-accent);
    }
  }
}

.credit-wrapper {
  max-height: 9rem;
  overflow: auto;
}

.credits, .credits li {
  display: inline;
}

.credits {
  display: inline;

  li {
    display: inline;

    &:last-child {
      &::before {
        content: ', and '
      }

      &::after {
        content: '.';
      }
    }
  }

  li + li {
    &::before {
      content: ', ';
    }
  }
}

.sponsors {
  margin-top: 1rem;
}
</style>
