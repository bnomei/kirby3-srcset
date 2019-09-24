<template>
  <div>
    <figure>
      <template v-if="attrs.src">
        <div
          ref="element"
          :style="style"
          :data-responsive="attrs.ratio"
          class="k-editor-image-block-wrapper"
          tabindex="0"
          @keydown.delete="$emit('remove')"
          @keydown.enter="$emit('append')"
          @keydown.up.prevent="$emit('prev')"
          @keydown.down.prevent="$emit('next')"
        >
          <img
            ref="image"
            :src="attrs.src"
            :key="attrs.src"
            @dblclick="selectFile"
            @load="onLoad"
          />
        </div>
        <div class="lazysrcset-icon">
          <k-button icon="cog" @click="settings"
            >{{ $t("editor.blocks.srcset.icon") }}
          </k-button>
        </div>
        <figcaption>
          <k-editable
            :content="attrs.caption"
            :breaks="true"
            :placeholder="$t('editor.blocks.srcset.caption.placeholder') + '…'"
            @input="caption"
          />
        </figcaption>
      </template>
      <template v-else>
        <div
          class="k-editor-image-block-placeholder"
          ref="element"
          tabindex="0"
        >
          <k-button icon="upload" @click="uploadFile"
            >{{ $t("editor.blocks.srcset.upload") }}
          </k-button>
          {{ $t("editor.blocks.srcset.or") }}
          <k-button icon="image" @click="selectFile"
            >{{ $t("editor.blocks.srcset.select") }}
          </k-button>
        </div>
      </template>
    </figure>
    <k-dialog ref="settings" @submit="saveSettings" size="medium">
      <k-form :fields="fields" v-model="attrs" @submit="saveSettings" />
    </k-dialog>

    <k-files-dialog
      ref="fileDialog"
      @submit="insertFile($event)"
    ></k-files-dialog>
    <k-upload ref="fileUpload" @success="insertUpload"></k-upload>
  </div>
</template>

<script>
export default {
  icon: "image",
  data() {
    return {
      options: []
    };
  },
  props: {
    endpoints: Object,
    attrs: {
      type: Object,
      default() {
        return {};
      }
    }
    // options: {
    //   type: Object,
    //   default() {
    //     return {};
    //   }
    // }
  },
  computed: {
    style() {
      if (this.attrs.ratio) {
        return "padding-bottom:" + 100 / this.attrs.ratio + "%";
      }
    },
    fields() {
      return {
        alt: {
          label: this.$t("editor.blocks.srcset.alt.label"),
          type: "text",
          icon: "text"
        },
        css: {
          label: this.$t("editor.blocks.srcset.css.label"), // class
          type: "text",
          icon: "code"
        },
        width: {
          label: this.$t("editor.blocks.srcset.width.label"),
          type: "number",
          after: "px"
        },
        height: {
          label: this.$t("editor.blocks.srcset.height.label"),
          type: "number",
          after: "px"
        },
        imgclass: {
          label: this.$t("editor.blocks.srcset.imgclass.label"), // class
          type: "text",
          icon: "code"
        },
        link: {
          label: this.$t("editor.blocks.srcset.link.label"),
          type: "text",
          icon: "url",
          placeholder: this.$t("editor.blocks.srcset.link.placeholder")
        },
        linkclass: {
          label: this.$t("editor.blocks.srcset.linkclass.label"),
          type: "text",
          icon: "code"
        },
        rel: {
          label: this.$t("editor.blocks.srcset.rel.label"),
          type: "text",
          icon: "code"
        },
        target: {
          label: this.$t("editor.blocks.srcset.target.label"),
          type: "text",
          icon: "code"
        },
        // text: {
        //   label: this.$t("editor.blocks.srcset.text.label"),
        //   type: "text",
        //   icon: "text"
        // },
        title: {
          label: this.$t("editor.blocks.srcset.title.label"),
          type: "text",
          icon: "text"
        },
        sizes: {
          label: this.$t("editor.blocks.srcset.sizes.label"),
          type: "text",
          icon: "code",
          placeholder: "default"
        },
        lazy: {
          label: this.$t("editor.blocks.srcset.lazy.label"),
          type: "text",
          icon: "code",
          placeholder: this.options.lazy
        },
        prefix: {
          label: this.$t("editor.blocks.srcset.prefix.label"),
          type: "text",
          icon: "code",
          placeholder: this.options.prefix
        },
        autosizes: {
          label: this.$t("editor.blocks.srcset.autosizes.label"),
          type: "text",
          icon: "code",
          placeholder: this.options.autosizes
        },
        quality: {
          label: this.$t("editor.blocks.srcset.quality.label"),
          type: "number",
          after: "px",
          placeholder: this.options.quality
        },
        figure: {
          label: this.$t("editor.blocks.srcset.figure.label"),
          type: "text",
          icon: "code",
          placeholder: this.options.figure
        }
      };
    }
  },
  mounted() {
    this.fetchOptions("bnomei/srcset/options");
  },
  methods: {
    fetchOptions(link) {
      this.$api.get(link).then(response => {
        this.$nextTick(() => {
          this.options = response;
        });
      });
    },
    caption(html) {
      this.input({
        caption: html
      });
    },
    edit() {
      if (this.attrs.guid) {
        this.$router.push(this.attrs.guid);
      }
    },
    focus() {
      this.$refs.element.focus();
    },
    input(data) {
      this.$emit("input", {
        attrs: {
          ...this.attrs,
          ...data
        }
      });
    },
    fetchFile(link) {
      this.$api.get(link).then(response => {
        this.input({
          guid: response.link,
          src: response.url,
          id: response.id,
          ratio: response.dimensions.ratio
        });
      });
    },
    insertFile(files) {
      const file = files[0];
      this.fetchFile(file.link);
    },
    insertUpload(files, response) {
      this.fetchFile(response[0].link);
    },
    menu() {
      if (this.attrs.src) {
        return [
          {
            icon: "open",
            label: this.$t("editor.blocks.srcset.open.browser"),
            clicks: this.open
          },
          {
            icon: "edit",
            label: this.$t("editor.blocks.srcset.open.panel"),
            click: this.edit,
            disabled: !this.attrs.guid
          },
          {
            icon: "cog",
            label: this.$t("editor.blocks.srcset.settings"),
            click: this.$refs.settings.open
          },
          {
            icon: "image",
            label: this.$t("editor.blocks.srcset.replace"),
            click: this.replace
          }
        ];
      } else {
        return [];
      }
    },
    open() {
      window.open(this.attrs.src);
    },
    onLoad() {
      const image = this.$refs.image;
      if (!this.attrs.ratio && image && image.width && image.height) {
        this.input({
          ratio: image.width / image.height
        });
      }
    },
    replace() {
      this.$emit("input", {
        attrs: {}
      });
    },
    selectFile() {
      this.$refs.fileDialog.open({
        endpoint: this.endpoints.field + "/files",
        multiple: false,
        selected: [this.attrs.id]
      });
    },
    settings() {
      this.$refs.settings.open();
    },
    saveSettings() {
      this.$refs.settings.close();
      this.input(this.attrs);
    },
    uploadFile() {
      this.$refs.fileUpload.open({
        url: window.panel.api + "/" + this.endpoints.field + "/upload",
        multiple: false,
        accept: "image/*"
      });
    }
  }
};
</script>

<style lang="scss">
.k-editor-image-block {
  margin: 1.5rem 0;
}

.k-editor-image-block figure {
  line-height: 0;
}

.k-editor-image-block-wrapper img {
  width: 100%;
}

.k-editor-image-block-wrapper[data-responsive] img {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  object-fit: contain;
  height: 100%;
}

.k-editor-image-block-wrapper[data-responsive] {
  position: relative;
  padding-bottom: 66.66%;
  background: #2d2e36;
}

.k-editor-image-block-wrapper:focus {
  outline: 0;
}

.k-editor-image-block-wrapper:focus img {
  outline: 2px solid rgba(#4271ae, 0.25);
  outline-offset: 2px;
}

.k-editor-image-block figcaption {
  display: block;
  margin-top: 0.75rem;
}

.k-editor-image-block .k-editable-placeholder,
.k-editor-image-block .ProseMirror {
  text-align: center;
  font-size: 0.875rem;
  line-height: 1.5em;
}

.k-editor-image-block-placeholder {
  display: flex;
  line-height: 1;
  justify-content: center;
  align-items: center;
  font-style: italic;
  font-size: 0.875rem;
  width: 100%;
  border: 1px dashed #ddd;
  border-radius: 3px;
  text-align: center;
  color: #bbb;
}

.k-editor-image-block-placeholder:focus {
  outline: 0;
}

.k-editor-image-block-placeholder .k-button {
  padding: 0.75rem;
  display: flex;
  align-items: center;
  color: #000;
  margin: 0 0.5rem;
}

.lazysrcset-icon {
  width: 100%;
  text-align: right;
}
.lazysrcset-icon > button {
  display: inline-block;
  color: white;
  text-align: right;
  width: 100%;
  background-color: black;
  padding: 0.25rem 0.5rem;
}
</style>
