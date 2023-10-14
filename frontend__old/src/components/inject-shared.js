// EXAMPLE: //
// import shared from "@/components/.../shared";
// import injectShared from "@/components/inject-shared";

// const componentStates = injectShared(shared, `${componentName}`);

// export default componentStates;

export default function (shared, componentName) {
    const componentStates = {
        name: componentName,
    };
    for (let key in shared) {
        const componentState = shared[key];
        componentStates[key] = componentState;
    }

    return componentStates;
}