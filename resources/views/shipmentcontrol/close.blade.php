 <!-- Modal -->
 <div class="modal fade" id="delete{{ $shipmentdetail->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">{{ $shipmentdetail->prod_code }}</h5>
            <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">Czy na pewno chcesz zakończyć ?
                <form id="delete-data" action="{{ route('control.closepos', $shipmentdetail->id) }}" method="POST" class="d-none">
                    @method('Post')
                    @csrf
                </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger px-4 btn-sm">Tak, usuń</button>
                        <button type="button" class="btn btn-secondary px-3 btn-sm" data-coreui-dismiss="modal">Anuluj</button>
                    </div>
                </form>

      </div>
    </div>
  </div>
