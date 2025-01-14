<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Three.js Simulatie</title>
  <style>
    body { margin: 0; overflow: hidden; }
    #cubeCounter {
      position: absolute;
      top: 10px;
      left: 10px;
      color: yellow;
      font-size: 18px;
      font-family: Arial, sans-serif;
    }
    #sphereCounter {
      position: absolute;
      top: 10px;
      right: 10px;
      color: blue;
      font-size: 18px;
      font-family: Arial, sans-serif;
    }
  </style>
</head>
<body>
  <div id="cubeCounter">Cubes: 0</div>
  <div id="sphereCounter">Spheres: 0</div>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
  
  <script>
    let cubeCount = 0;
    let sphereCount = 0;
    const cubes = [];
    const spheres = [];
    const velocities = [];

    // Scene genereerder
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x000000);  // Black void

    // Camera Variabel
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.z = 20;

    // WEBGL Renderer
    const renderer = new THREE.WebGLRenderer();
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    const cubeGeometry = new THREE.BoxGeometry(1, 1, 1);
    const cubeMaterial = new THREE.MeshBasicMaterial({ color: 0xffff00 });
    const sphereGeometry = new THREE.SphereGeometry(0.5, 32, 32);
    const sphereMaterial = new THREE.MeshBasicMaterial({ color: 0x0000ff });

    const boundingBoxSize = 30;
    const boxMin = -boundingBoxSize;
    const boxMax = boundingBoxSize;

    const center = new THREE.Vector3(0, 0, 0); 

    // Function to add cubes
    function addCube() {
      const cube = new THREE.Mesh(cubeGeometry, cubeMaterial);
      cube.position.set(Math.random() * 20 - 10, Math.random() * 20 - 10, Math.random() * 20 - 10);
      scene.add(cube);
      cubes.push(cube);
      velocities.push(new THREE.Vector3(0, 0, 0)); // Add velocity vector
      cubeCount++;
      document.getElementById('cubeCounter').textContent = `Blokken: ${cubeCount}`;
    }

    // Function to add spheres
    function addSphere() {
      const sphere = new THREE.Mesh(sphereGeometry, sphereMaterial);
      sphere.position.set(Math.random() * 20 - 10, Math.random() * 20 - 10, Math.random() * 20 - 10);
      scene.add(sphere);
      spheres.push(sphere);
      velocities.push(new THREE.Vector3(0, 0, 0)); // Add velocity vector
      sphereCount++;
      document.getElementById('sphereCounter').textContent = `Bollen: ${sphereCount}`;
    }

    // Function to remove a random cube
    function removeCube() {
      if (cubes.length > 0) {
        const index = Math.floor(Math.random() * cubes.length);
        scene.remove(cubes[index]);
        cubes.splice(index, 1);
        velocities.splice(index, 1); // Remove velocity vector
        cubeCount--;
        document.getElementById('cubeCounter').textContent = `Blokken: ${cubeCount}`;
      }
    }

    // Function to remove a random sphere
    function removeSphere() {
      if (spheres.length > 0) {
        const index = Math.floor(Math.random() * spheres.length);
        scene.remove(spheres[index]);
        spheres.splice(index, 1);
        velocities.splice(index + cubes.length, 1); // Remove velocity vector
        sphereCount--;
        document.getElementById('sphereCounter').textContent = `Bollen: ${sphereCount}`;
      }
    }

    // Initialize 25 cubes and 25 spheres
    for (let i = 0; i < 25; i++) {
      addCube();
      addSphere();
    }

    // Update velocities based on direction from center
    function updateVelocities() {
      const objects = [...cubes, ...spheres];

      objects.forEach((obj, index) => {
        const direction = new THREE.Vector3();
        direction.subVectors(obj.position, center);
        direction.normalize();

        // Apply a small velocity away from the center
        velocities[index].addScaledVector(direction, 0.0005); // You can tweak the speed here

        // Update the position of the object based on the velocity
        obj.position.add(velocities[index]);

        if (obj.position.x <= boxMin || obj.position.x >= boxMax) {
          velocities[index].x *= -1;  // Reverse X velocity
        }
        if (obj.position.y <= boxMin || obj.position.y >= boxMax) {
          velocities[index].y *= -1;  // Reverse Y velocity
        }
        if (obj.position.z <= boxMin || obj.position.z >= boxMax) {
          velocities[index].z *= -1;  // Reverse Z velocity
        }
      });
    }

    // Event listener for keypresses
    window.addEventListener('keydown', (event) => {
      switch (event.key) {
        case 'w': // W key to add cube
        case 'W':
          addCube();
          break;
        case 's': // S key to remove cube
        case 'S':
          removeCube();
          break;
        case 'ArrowUp': // Up arrow to add sphere
          addSphere();
          break;
        case 'ArrowDown': // Down arrow to remove sphere
          removeSphere();
          break;
      }
    });

    // Animation loop
    function animate() {
      requestAnimationFrame(animate);
      controls.update();

      // Update positions of objects based on their velocities
      updateVelocities();

      renderer.render(scene, camera);
    }

    animate();
  </script>
</body>
</html>
