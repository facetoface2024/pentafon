declare module 'three/examples/jsm/loaders/GLTFLoader.js' {
    import { Loader, LoadingManager, Group, AnimationClip } from 'three';

    export interface GLTF {
        animations: AnimationClip[];
        scene: Group;
        scenes: Group[];
        cameras: any[];
        asset: any;
    }

    export class GLTFLoader extends Loader {
        constructor(manager?: LoadingManager);
        load(
            url: string,
            onLoad: (gltf: GLTF) => void,
            onProgress?: (event: ProgressEvent) => void,
            onError?: (event: ErrorEvent) => void
        ): void;
        loadAsync(url: string): Promise<GLTF>;
    }
}

declare module 'three/examples/jsm/controls/OrbitControls.js' {
    import { Camera, MOUSE, Object3D, TOUCH, Vector3 } from 'three';

    export class OrbitControls {
        constructor(object: Camera, domElement?: HTMLElement);

        object: Camera;
        domElement: HTMLElement | Document;

        enabled: boolean;
        target: Vector3;

        enableZoom: boolean;
        zoomSpeed: number;
        minDistance: number;
        maxDistance: number;

        enableRotate: boolean;
        rotateSpeed: number;

        enablePan: boolean;
        panSpeed: number;

        enableDamping: boolean;
        dampingFactor: number;

        minAzimuthAngle: number;
        maxAzimuthAngle: number;

        minPolarAngle: number;
        maxPolarAngle: number;

        autoRotate: boolean;
        autoRotateSpeed: number;

        mouseButtons: {
            LEFT?: MOUSE;
            MIDDLE?: MOUSE;
            RIGHT?: MOUSE;
        };

        touches: {
            ONE?: TOUCH;
            TWO?: TOUCH;
        };

        update(): boolean;
        dispose(): void;
    }
}
