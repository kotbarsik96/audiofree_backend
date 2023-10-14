export default {
    setContentClassNames(componentType = "spoiler") {
        if (componentType === "spoiler") {
            const contentContainers = this.$refs.contentContainer;
            contentContainers.forEach((containerNode, index) => {
                containerNode.className += ` ${this.titles[index].contentContainerClass}` || "";
                setContentChildrenClasses.call(this, containerNode);
            });
        }
        if (componentType === "tab") {
            const containerNode = this.$refs.contentContainer;
            const containerNodeClasses = this.titles[this.activeTitle].contentContainerClass;
            containerNode.className = containerNodeClasses ? `tab__content ${containerNodeClasses}` : "tab__content";
            setContentChildrenClasses.call(this, containerNode);
        }

        function setContentChildrenClasses(containerNode) {
            const childNodes = Array.from(containerNode.childNodes).filter(child => child.nodeType !== 3);

            // нужно, чтобы если был встречен класс компонента transition (или другого), он не повлиял бы на добавление дефолтных классов
            const exceptions = ["-enter-to", "-enter-active", "-enter-from", "-leave-to", "-leave-active", "-leave-from"];
            const regExps = exceptions.map(str => new RegExp(str));

            childNodes.forEach(child => {
                // убрать из className все классы-исключения (например, от компонента transition)
                let classNameArr = child.className.split(" ");
                regExps.forEach(rg => {
                    classNameArr.forEach(str => {
                        if(rg.test(str)) {
                            const index = classNameArr.indexOf(str);
                            classNameArr.splice(index, 1);
                        }
                    });
                });
                let className = classNameArr.join(" ");
                
                // если даже без исключений есть другие классы, не применять дефолтных
                if (className) return;
                // применить дефолтные, если нет классов
                switch (child.tagName) {
                    case "H5":
                    case "H4":
                    case "H3":
                        child.className = this.titleClassName;
                        break;
                    case "DIV":
                    case "P":
                        child.className = this.paragraphClassName;
                        break;
                }
            });
        }
    }
}