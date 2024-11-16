export default {
    $resetItems() {
        this.items = [];
        this.has_more = true;
    },
    $resetAll() {
        this.$resetItems();
        this.page = 1;
    },
    $loadTasks(events) {
        axios
            .get(route("upi.tasks"), {
                params: { page: this.page },
            })
            .then(({ data }) => {
                if (this.page === 1) {
                    this.$resetItems();
                }

                this.page++;
                this.items.push(...data.items);
                this.total = data.total;
                this.has_more = data.has_more;

                events.success();
            })
            .catch(({ response }) => {
                events.error(response);
            })
            .finally(() => {
                events.finish();
            });
    },
};
