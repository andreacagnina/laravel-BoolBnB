 {{-- <form action="{{ route('admin.properties.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <!-- Title -->
                    <div class="col-12">
                        <label for="title" class="form-label">Title:</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            class="form-control @error('title') is-invalid @enderror" required maxlength="50">
                        @error('title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Cover Image -->
                    <div class="col-12">
                        <label for="cover_image" class="form-label">Cover Image:</label>
                        <input type="file" name="cover_image" id="cover_image"
                            class="form-control @error('cover_image') is-invalid @enderror">
                        @error('cover_image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="description" class="form-label">Description:</label>
                        <textarea name="description" id="description" rows="5"
                            class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Number of Rooms -->
                    <div class="col-4">
                        <label for="num_rooms" class="form-label">Number of Rooms:</label>
                        <input type="number" name="num_rooms" id="num_rooms" value="{{ old('num_rooms', 1) }}"
                            class="form-control @error('num_rooms') is-invalid @enderror" min="1" max="50"
                            required>
                        @error('num_rooms')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Number of Beds -->
                    <div class="col-4">
                        <label for="num_beds" class="form-label">Number of Beds:</label>
                        <input type="number" name="num_beds" id="num_beds" value="{{ old('num_beds', 1) }}"
                            class="form-control @error('num_beds') is-invalid @enderror" min="1" max="20"
                            required>
                        @error('num_beds')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Number of Bathrooms -->
                    <div class="col-4">
                        <label for="num_baths" class="form-label">Number of Bathrooms:</label>
                        <input type="number" name="num_baths" id="num_baths" value="{{ old('num_baths', 0) }}"
                            class="form-control @error('num_baths') is-invalid @enderror" min="0" max="5"
                            required>
                        @error('num_baths')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Square Meters -->
                    <div class="col-12">
                        <label for="mq" class="form-label">Square Meters (mq):</label>
                        <input type="number" name="mq" id="mq" value="{{ old('mq') }}"
                            class="form-control @error('mq') is-invalid @enderror" min="10" max="5000" required>
                        @error('mq')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- ZIP -->
                    <div class="col-3">
                        <label for="zip" class="form-label">ZIP:</label>
                        <input type="text" name="zip" id="zip" value="{{ old('zip') }}"
                            class="form-control @error('zip') is-invalid @enderror" required minlength="5" maxlength="5">
                        @error('zip')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="col-3">
                        <label for="city" class="form-label">City:</label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}"
                            class="form-control @error('city') is-invalid @enderror" required minlength="2" maxlength="50">
                        @error('city')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="col-12">
                        <label for="address" class="form-label">Address:</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                            class="form-control @error('address') is-invalid @enderror" required minlength="2"
                            maxlength="100">
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Latitude -->
                    <div class="col-6">
                        <label for="lat" class="form-label">Latitude:</label>
                        <input type="text" name="lat" id="lat" value="{{ old('lat') }}"
                            class="form-control @error('lat') is-invalid @enderror">
                        @error('lat')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Longitude -->
                    <div class="col-6">
                        <label for="long" class="form-label">Longitude:</label>
                        <input type="text" name="long" id="long" value="{{ old('long') }}"
                            class="form-control @error('long') is-invalid @enderror">
                        @error('long')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="col-12">
                        <label for="price" class="form-label">Price:</label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}"
                            class="form-control @error('price') is-invalid @enderror" min="10" max="999999.99"
                            step="0.01" required>
                        @error('price')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div class="col-12">
                        <label for="type" class="form-label">Type:</label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror"
                            required>
                            <option value="" disabled selected>- Choose a Type -</option>
                            <option value="mansion" @selected(old('type') === 'mansion')>Mansion</option>
                            <option value="ski-in/out" @selected(old('type') === 'ski-in/out')>Ski-in/out</option>
                            <option value="tree-house" @selected(old('type') === 'tree-house')>Tree House</option>
                            <option value="apartment" @selected(old('type') === 'apartment')>Apartment</option>
                            <option value="dome" @selected(old('type') === 'dome')>Dome</option>
                            <option value="cave" @selected(old('type') === 'cave')>Cave</option>
                            <option value="cabin" @selected(old('type') === 'cabin')>Cabin</option>
                            <option value="lake" @selected(old('type') === 'lake')>Lake</option>
                            <option value="beach" @selected(old('type') === 'beach')>Beach</option>
                            <option value="castle" @selected(old('type') === 'castle')>Castle</option>
                        </select>
                        @error('type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Floor -->
                    <div class="col-12">
                        <label for="floor" class="form-label">Floor:</label>
                        <input type="number" name="floor" id="floor" value="{{ old('floor') }}"
                            class="form-control @error('floor') is-invalid @enderror" required>
                        @error('floor')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Available -->
                    <div class="col-12">
                        <label for="available" class="form-label">Available:</label>
                        <input type="radio" name="available" value="1" @checked(old('available') === '1') required> Yes
                        <input type="radio" name="available" value="0" @checked(old('available') === '0') required> No
                        @error('available')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Sponsored -->
                    <div class="col-12">
                        <label for="sponsored" class="form-label">Sponsored:</label>
                        <input type="checkbox" name="sponsored" id="sponsored" value="1"
                            @checked(old('sponsored') == '1')>
                        @error('sponsored')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary mt-4">Submit</button>
                </form> --}}
