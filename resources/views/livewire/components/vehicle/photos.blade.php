<div class="card card-absolute">
    <div class="card-header bg-default">
        <h5 class="text-dark">Vehicle Images </h5>
    </div>
    <div class="card-body">

    @if(count($photos)>0)

    <div class="row">
    
    <div class="col-lg-4">
        <div class="figure d-block">
        <blockquote class="blockquote">
        <p class="mb-0">
            @foreach($photos as $photo)
                @if(Str::containsAll(strtolower($photo->description), ["front"]))
                    <a href="{{ Asset(Storage::url($photo->path)) }}" target="_blank"><img src="{{ Asset(Storage::url($photo->path)) }}" width="400" height="300"  class="img-thumbnail"/></a>
                @endif
            @endforeach
        </p>
       <br /> <br />
        <div class="blockquote-footer">Front Side</div>
        </blockquote>
        </div>
            
        <div class="figure d-block">
            <blockquote class="blockquote">
            <p class="mb-0">
                @foreach($photos as $photo)
                    @if(Str::containsAll(strtolower($photo->description), ["back"]))
                        <a href="{{ Asset(Storage::url($photo->path)) }}" target="_blank"><img src="{{ Asset(Storage::url($photo->path)) }}" width="400" height="300"  class="img-thumbnail"/></a>
                    @endif
                @endforeach
            </p>
           <br /> <br />
            <div class="blockquote-footer">Back Side</div>
            </blockquote>
        </div>

        </div></div></div>


        <div class="col-lg-4">

            <div class="figure d-block">
                <blockquote class="blockquote">
                <p class="mb-0">
                    @foreach($photos as $photo)
                        @if(Str::containsAll(strtolower($photo->description), ["right"]))
                            <a href="{{ Asset(Storage::url($photo->path)) }}" target="_blank"><img src="{{ Asset(Storage::url($photo->path)) }}" width="400" height="300"  class="img-thumbnail"/></a>
                        @endif
                    @endforeach
                </p>
               <br /> <br />
                <div class="blockquote-footer">Right Side</div>
                </blockquote>
            </div>

            <div class="figure d-block">
                <blockquote class="blockquote">
                <p class="mb-0">
                    @foreach($photos as $photo)
                        @if(Str::containsAll(strtolower($photo->description), ["left"]))
                            <a href="{{ Asset(Storage::url($photo->path)) }}" target="_blank"><img src="{{ Asset(Storage::url($photo->path)) }}" width="400" height="300"  class="img-thumbnail"/></a>
                        @endif
                    @endforeach
                </p>
               <br /> <br />
                <div class="blockquote-footer">Left Side</div>
                </blockquote>
            </div>

        </div>

        <div class="col-lg-4">

              <div class="figure d-block">
                <blockquote class="blockquote">
                <p class="mb-0">
                    @foreach($photos as $photo)
                        @if(Str::containsAll(strtolower($photo->description), ["odometer"]))
                            <a href="{{ Asset(Storage::url($photo->path)) }}" target="_blank"><img src="{{ Asset(Storage::url($photo->path)) }}" width="400" height="300"  class="img-thumbnail"/></a>
                        @endif
                    @endforeach
                </p>
               <br /> <br />
                <div class="blockquote-footer">Odometer</div>
                </blockquote>
                </div>

                <div class="figure d-block">
                    <blockquote class="blockquote">
                    <p class="mb-0">
                        @foreach($photos as $photo)
                            @if(Str::containsAll(strtolower($photo->description), ["chasis"]))
                                <a href="{{ Asset(Storage::url($photo->path)) }}" target="_blank"><img src="{{ Asset(Storage::url($photo->path)) }}" width="400" height="300"  class="img-thumbnail"/></a>
                            @endif
                        @endforeach
                    </p>
               <br /> <br />
                <div class="blockquote-footer">Chasis</div>
                </blockquote>
                </div>
        @else 
            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
            <i class="icon-info-alt txt-danger"></i>
                No Records Found yet
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
            </div>

        </div>

        @endif

     </div>
</div>